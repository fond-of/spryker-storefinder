<?php

namespace FondOfSpryker\Yves\Storefinder\Plugin\Router;

use FondOfSpryker\Shared\Storefinder\StorefinderConstants;
use Spryker\Yves\Router\Plugin\RouteProvider\AbstractRouteProviderPlugin;
use Spryker\Yves\Router\Route\RouteCollection;

class StorefinderRouteProviderPlugin extends AbstractRouteProviderPlugin
{
    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $routeCollection = $this->addStorefinderIndexRoute($routeCollection);
        $routeCollection = $this->addStorefinderSearchRoute($routeCollection);
        $routeCollection = $this->addStorefinderDetailRoute($routeCollection);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addStorefinderIndexRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/storefinder', 'Storefinder', 'Index', 'index');
        $route = $route->setMethods(['GET']);
        $routeCollection->add(StorefinderConstants::ROUTE_STOREFINDER_INDEX, $route);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addStorefinderSearchRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/storefinder/search', 'Storefinder', 'Index', 'search');
        $route = $route->setMethods(['GET']);
        $routeCollection->add(StorefinderConstants::ROUTE_STOREFINDER_SEARCH, $route);

        return $routeCollection;
    }

    /**
     * @param \Spryker\Yves\Router\Route\RouteCollection $routeCollection
     *
     * @return \Spryker\Yves\Router\Route\RouteCollection
     */
    protected function addStorefinderDetailRoute(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('/storefinder/{urlKey}', 'Storefinder', 'Index', 'detail');
        $route = $route->setMethods(['GET']);
        $routeCollection->add(StorefinderConstants::ROUTE_STOREFINDER_DETAIL, $route);

        return $routeCollection;
    }
}
