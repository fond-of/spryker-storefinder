<?php

namespace FondOfSpryker\Client\Storefinder;

use Elasticsearch\ClientBuilder;
use GuzzleHttp\Client;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

class StorefinderDependencyProvider extends AbstractDependencyProvider
{
    public const ELASTICSEARCH_CLIENT_BUILDER = 'ELASTICSEARCH_CLIENT_BUILDER';
    public const HTTP_CLIENT = 'HTTP_CLIENT';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);
        $container = $this->provideElasticsearchClientBuilder($container);
        $container = $this->provideHttpClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function provideElasticsearchClientBuilder(Container $container): Container
    {
        $container[static::ELASTICSEARCH_CLIENT_BUILDER] = function () {
            return new ClientBuilder();
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function provideHttpClient(Container $container): Container
    {
        $container[static::HTTP_CLIENT] = function () {
            return new Client();
        };

        return $container;
    }
}
