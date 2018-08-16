<?php

namespace FondOfSpryker\Yves\Storefinder\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class StorefinderControllerProvider extends AbstractYvesControllerProvider
{
    const STOREFINDER_INDEX = 'storefinder-index';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app)
    {
        $this->addStorefinderRoute();
    }

    /**
     * @return void
     */
    protected function addStorefinderRoute(): void
    {
        $this->createController('/{storefinder}', self::STOREFINDER_INDEX, 'Storefinder', 'Index')
            ->method('GET')
            ->assert('storefinder', $this->getAllowedLocalesPattern(). 'storefinder|storefinder')
            ->value('storefinder', '');
    }
}
