<?php

namespace FondOfSpryker\Client\Storefinder;

use FondOfSpryker\Shared\Storefinder\StorefinderConstants;
use Spryker\Client\Kernel\AbstractBundleConfig;

class StorefinderConfig extends AbstractBundleConfig
{
    /**
     * @return string
     */
    public function getStorefinderElasticsearchHost()
    {
        return $this->get(StorefinderConstants::STOREFINDER_ELASTICSEARCH_HOST);
    }

    /**
     * @return string
     */
    public function getStorefinderBrand()
    {
        return $this->get(StorefinderConstants::STOREFINDER_BRAND);
    }

    /**
     * @return string
     */
    public function getStorefinderGoogleApiKey()
    {
        return $this->get(StorefinderConstants::STOREFINDER_GOOGLE_API_KEY);
    }
}
