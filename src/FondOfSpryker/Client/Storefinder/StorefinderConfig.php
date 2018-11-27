<?php

namespace FondOfSpryker\Client\Storefinder;

use FondOfSpryker\Shared\Storefinder\StorefinderConstants;
use Spryker\Client\Kernel\AbstractBundleConfig;

class StorefinderConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getStorefinderElasticsearchHost(): string
    {
        return $this->get(StorefinderConstants::STOREFINDER_ELASTICSEARCH_HOST);
    }

    /**
     * @return string
     */
    public function getStorefinderBrand(): string
    {
        return $this->get(StorefinderConstants::STOREFINDER_BRAND);
    }

    /**
     * @return string
     */
    public function getStorefinderGoogleApiKey(): string
    {
        return $this->get(StorefinderConstants::STOREFINDER_GOOGLE_API_KEY);
    }
}
