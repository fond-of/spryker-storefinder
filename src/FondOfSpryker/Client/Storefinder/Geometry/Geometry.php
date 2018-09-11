<?php

namespace FondOfSpryker\Client\Storefinder\Geometry;

use Generated\Shared\Transfer\StorefinderGeometryRequestTransfer;
use Generated\Shared\Transfer\StorefinderGeometryResponseTransfer;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class Geometry implements GeometryInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     * @param string $apiKey
     */
    public function __construct(ClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    protected function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderGeometryRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\StorefinderGeometryResponseTransfer|null
     */
    public function findGeometryBy(StorefinderGeometryRequestTransfer $requestTransfer): ?StorefinderGeometryResponseTransfer
    {
        $clientJsonResponse = $this->getClient()->request('GET', 'https://maps.google.com/maps/api/geocode/json', $this->createApiQuery($requestTransfer));

        if ($this->isValidClientResponse($clientJsonResponse) === false) {
            return null;
        }

        return $this->createGeometryResponse($clientJsonResponse);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $clientJsonResponse
     *
     * @return bool
     */
    protected function isValidClientResponse(ResponseInterface $clientJsonResponse): bool
    {
        if ($clientJsonResponse->getStatusCode() != 200) {
            return false;
        }

        $decodedClientJsonResponse = $this->jsonDecodeResponse($clientJsonResponse);
        if (is_array($decodedClientJsonResponse) === false) {
            return false;
        }

        if (array_key_exists('results', $decodedClientJsonResponse) === false) {
            return false;
        }

        if (is_array($decodedClientJsonResponse['results']) === false) {
            return false;
        }

        if (count($decodedClientJsonResponse['results']) === 0) {
            return false;
        }

        $firstResult = array_shift($decodedClientJsonResponse['results']);
        if (array_key_exists('geometry', $firstResult) === false) {
            return false;
        }

        if (array_key_exists('location', $firstResult['geometry']) === false) {
            return false;
        }

        if (array_key_exists('lat', $firstResult['geometry']['location']) === false) {
            return false;
        }

        return true;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $clientJsonResponse
     *
     * @return mixed
     */
    protected function jsonDecodeResponse(ResponseInterface $clientJsonResponse)
    {
        return json_decode($clientJsonResponse->getBody(), true);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $clientJsonResponse
     *
     * @return \Generated\Shared\Transfer\StorefinderGeometryResponseTransfer
     */
    protected function createGeometryResponse(ResponseInterface $clientJsonResponse): StorefinderGeometryResponseTransfer
    {
        $decodedClientJsonResponse = $this->jsonDecodeResponse($clientJsonResponse);
        $latitude = $decodedClientJsonResponse['results'][0]['geometry']['location']['lat'];
        $longitude = $decodedClientJsonResponse['results'][0]['geometry']['location']['lng'];

        $geometryResponse = new StorefinderGeometryResponseTransfer();
        $geometryResponse->setLatitude($latitude);
        $geometryResponse->setLongitude($longitude);

        return $geometryResponse;
    }

    /**
     * @return string[]
     */
    protected function createApiQuery(StorefinderGeometryRequestTransfer $requestTransfer): array
    {
        $queryParameters = [];
        if ($this->apiKey !== '') {
            $queryParameters['key'] = $this->apiKey;
        }

        if ($requestTransfer->getAddress() !== null) {
            $queryParameters['address'] = $requestTransfer->getAddress();
        }

        if ($requestTransfer->getCountryCode() !== null) {
            $queryParameters['components'] = sprintf('country:%s', $requestTransfer->getCountryCode());
        }

        return ['query' => $queryParameters];
    }
}
