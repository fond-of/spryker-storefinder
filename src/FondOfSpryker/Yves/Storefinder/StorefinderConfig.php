<?php

namespace FondOfSpryker\Yves\Storefinder;

use FondOfSpryker\Shared\Storefinder\StorefinderConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;

class StorefinderConfig extends AbstractBundleConfig
{
    /**
     * @return array
     */
    public function getCountries(): array
    {
        return $this->get(StorefinderConstants::STOREFINDER_COUNTRIES, []);
    }
}
