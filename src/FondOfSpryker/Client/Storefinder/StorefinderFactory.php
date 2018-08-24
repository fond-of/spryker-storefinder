<?php

namespace FondOfSpryker\Client\Storefinder;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use FondOfSpryker\Client\Storefinder\Search\CustomerAddressRepository;
use FondOfSpryker\Client\Storefinder\Search\ResponseToTransferMapper;
use Spryker\Client\Kernel\AbstractFactory;
use FondOfSpryker\Client\Storefinder\Search\ResponseToTransferMapperInterface;
use FondOfSpryker\Client\Storefinder\Geometry\GeometryInterface;
use FondOfSpryker\Client\Storefinder\Geometry\Geometry;
use GuzzleHttp\ClientInterface;

/**
 * @method \FondOfSpryker\Client\Storefinder\StorefinderConfig getConfig()
 */
class StorefinderFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Client\Storefinder\Search\CustomerAddressRepository
     */
    public function createCustomerAddressRepository(): CustomerAddressRepository
    {
        return new CustomerAddressRepository(
            $this->createElasticsearchClient(),
            $this->createResponseToTransferMapper(),
            $this->createGeometry(),
            $this->getConfig()->getStorefinderBrand()
        );
    }

    /**
     * @return \FondOfSpryker\Client\Storefinder\Geometry\GeometryInterface
     */
    protected function createGeometry(): GeometryInterface
    {
        return new Geometry(
            $this->getHttpClient(),
            $this->getConfig()->getStorefinderGoogleApiKey()
        );
    }

    /**
     * @return \FondOfSpryker\Client\Storefinder\Search\ResponseToTransferMapperInterface
     */
    public function createResponseToTransferMapper(): ResponseToTransferMapperInterface
    {
        return new ResponseToTransferMapper();
    }

    /**
     * @return \FondOfSpryker\Client\Storefinder\Client
     */
    protected function createElasticsearchClient(): Client
    {
        $clientBuilder = $this->getElasticsearchClientBuilder();
        $clientBuilder->setHosts([$this->getConfig()->getStorefinderElasticsearchHost()]);

        return $clientBuilder->build();
    }

    /**
     * @return \Elasticsearch\ClientBuilder
     */
    protected function getElasticsearchClientBuilder(): ClientBuilder
    {
        return $this->getProvidedDependency(StorefinderDependencyProvider::ELASTICSEARCH_CLIENT_BUILDER);
    }

    /**
     * @return \GuzzleHttp\ClientInterface;
     */
    protected function getHttpClient(): ClientInterface
    {
        return $this->getProvidedDependency(StorefinderDependencyProvider::HTTP_CLIENT);
    }
}
