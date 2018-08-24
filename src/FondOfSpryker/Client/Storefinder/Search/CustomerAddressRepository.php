<?php

namespace FondOfSpryker\Client\Storefinder\Search;

use Elasticsearch\Client;
use FondOFSpryker\Client\Storefinder\Geometry\GeometryInterface;
use Generated\Shared\Transfer\StorefinderCustomerAddressTransfer;
use Generated\Shared\Transfer\StorefinderGeometryRequestTransfer;
use Generated\Shared\Transfer\StorefinderGeometryResponseTransfer;
use Generated\Shared\Transfer\StorefinderSearchRequestTransfer;
use Generated\Shared\Transfer\StorefinderSearchResponseTransfer;

class CustomerAddressRepository
{
    /**
     * @var Elasticsearch/Client
     */
    protected $client;

    /**
     * @var \FondOfSpryker\Client\Storefinder\Search\ResponseToTransferMapperInterface
     */
    protected $responseToTransferMapper;

    /**
     * @var \FondOFSpryker\Client\Storefinder\Geometry\GeometryInterface
     */
    protected $geometry;

    /**
     * @var string
     */
    protected $brand;

    /**
     * @param \Elasticsearch\Client $client
     * @param \FondOfSpryker\Client\Storefinder\Search\ResponseToTransferMapperInterface $responseToTransferMapper
     * @param \FondOFSpryker\Client\Storefinder\Geometry\GeometryInterface $geometry
     * @param string $brand
     */
    public function __construct(Client $client, ResponseToTransferMapperInterface $responseToTransferMapper, GeometryInterface $geometry, string $brand)
    {
        $this->client = $client;
        $this->responseToTransferMapper = $responseToTransferMapper;
        $this->geometry = $geometry;
        $this->brand = $brand;
    }

    /**
     * @return \Elasticsearch\Client
     */
    protected function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return \FondOfSpryker\Client\Storefinder\Search\ResponseToTransferMapperInterface
     */
    protected function getResponseToTransferMapper(): ResponseToTransferMapperInterface
    {
        return $this->responseToTransferMapper;
    }

    /**
     * @return \FondOFSpryker\Client\Storefinder\Geometry\GeometryInterface
     */
    protected function getGeometry(): GeometryInterface
    {
        return $this->geometry;
    }

    /**
     * @return string
     */
    protected function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $urlKey
     *
     * @return null|\Generated\Shared\Transfer\StorefinderCustomerAddressTransfer
     */
    public function findOneByUrlKey(string $urlKey): ?StorefinderCustomerAddressTransfer
    {
        $params = [];
        $params['index'] = 'partner';
        $params['type'] = 'customer_address';
        $params['body'] = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['match' => ['custaddr_url_key' => $urlKey]],
                        ['match' => ['brands' => $this->getBrand()]],
                    ]
                ],
            ],
        ];

        $elasticsearchResponse = $this->getClient()->search($params);
        $searchResponseTransfer = $this->getResponseToTransferMapper()->mapResponseToTransfer($elasticsearchResponse);


        $result = $searchResponseTransfer->getResult();
        $resultCount = $searchResponseTransfer->getResultCount();

        if ($resultCount == 0) {
            return null;
        }

        return $result->offsetGet(0);
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderSearchRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function search(StorefinderSearchRequestTransfer $requestTransfer): StorefinderSearchResponseTransfer
    {
        $params = [];
        $params['index'] = 'partner';
        $params['type'] = 'customer_address';
        $params['body'] = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['match' => ['brands' => $this->getBrand()]],
                    ]
                ],
            ],
        ];

        if (! empty($requestTransfer->getCountryCode()) && ! empty($requestTransfer->getAddress())) {
            $geometryResponse = $this->findLatitudeAndLongitudeBy($requestTransfer);

            if ($geometryResponse !== null) {
                // filter radial by lat/lng if given
                $params['body']['query']['bool']['filter'] = [
                    'geo_distance' => [
                        'distance' => '50km',
                        'fob_location' => [
                            'lat' => $geometryResponse->getLatitude(),
                            'lon' => $geometryResponse->getLongitude()
                        ]
                    ]
                ];

                // sort radial by lat/lng if given
                $params['body']['sort'] = [
                    '_geo_distance' => [
                        'fob_location' => [
                            'lat' => $geometryResponse->getLatitude(),
                            'lon' => $geometryResponse->getLongitude()
                        ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
                ];
            }

        } else if ($requestTransfer->getCountryCode() !== null) {
            // filter by country_id
            $params['body']['query']['bool']['must'][]['match']['country_id'] = $requestTransfer->getCountryCode();
        }

        // sort by zip code by default
        if (array_key_exists('sort', $params['body']) === false) {
            $params['body']['sort'] = [
                'postcode' => [
                    'order' => 'asc'
                ]
            ];
        }

        // pagination
        if (is_int($requestTransfer->getLimit())) {
            $params['size'] = $requestTransfer->getLimit();
        } else {
            $params['size'] = $this->getClient()->count($params);
        }

        if (is_int($requestTransfer->getLimitStart())) {
            $params['from'] = $requestTransfer->getLimitStart();
        }

        $elasticsearchResponse = $this->getClient()->search($params);

        return $this->getResponseToTransferMapper()->mapResponseToTransfer($elasticsearchResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderSearchRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\StorefinderGeometryResponseTransfer|null
     */
    protected function findLatitudeAndLongitudeBy(StorefinderSearchRequestTransfer $requestTransfer): ?StorefinderGeometryResponseTransfer
    {
        $geometryRequest = new StorefinderGeometryRequestTransfer();
        $geometryRequest->setCountryCode($requestTransfer->getCountryCode());
        $geometryRequest->setAddress($requestTransfer->getAddress());

        return $this->getGeometry()->findGeometryBy($geometryRequest);
    }
}
