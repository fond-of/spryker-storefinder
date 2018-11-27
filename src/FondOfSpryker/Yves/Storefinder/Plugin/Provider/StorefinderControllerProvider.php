<?php

namespace FondOfSpryker\Yves\Storefinder\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class StorefinderControllerProvider extends AbstractYvesControllerProvider
{
    public const STOREFINDER_INDEX = 'storefinder-index';
    public const STOREFINDER_DETAIL = 'storefinder-detail';
    public const STOREFINDER_SEARCH = 'storefinder-search';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app): void
    {
        // add search route before detail route
        $this->addStorefinderIndexRoute();
        $this->addStorefinderSearchRoute();
        $this->addStorefinderDetailRoute();
    }

    /**
     * @return void
     */
    protected function addStorefinderIndexRoute(): void
    {
        $this->createController('/{storefinder}', self::STOREFINDER_INDEX, 'Storefinder', 'Index')
            ->method('GET')
            ->assert('storefinder', $this->getAllowedLocalesPattern() . 'storefinder|storefinder')
            ->value('storefinder', '');
    }

    /**
     * @return void
     */
    protected function addStorefinderSearchRoute(): void
    {
        $this->createController('/{storefinder}/search', self::STOREFINDER_SEARCH, 'Storefinder', 'Index', 'search')
            ->method('GET')
            ->assert('storefinder', $this->getAllowedLocalesPattern() . 'storefinder|storefinder')
            ->value('storefinder', '');
    }

    /**
     * @return void
     */
    protected function addStorefinderDetailRoute(): void
    {
        $this->createController('/{storefinder}/{urlKey}', self::STOREFINDER_DETAIL, 'Storefinder', 'Index', 'detail')
            ->method('GET')
            ->assert('storefinder', $this->getAllowedLocalesPattern() . 'storefinder|storefinder')
            ->value('storefinder', '');
    }
}
