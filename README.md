# fond-of-spryker/storefinder
[![PHP from Travis config](https://img.shields.io/travis/php-v/symfony/symfony.svg)](https://php.net/)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/fond-of-spryker/storefinder)

## Installation

```
composer require fond-of-spryker/storefinder
```

## Configuration

### Silex Routing (deprecated use Symfony Routing instead)
add in src/Pyz/Yves/ShopApplication/YvesBootstrap.php
```
    /**
     * @param bool|null $isSsl
     *
     * @return \SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider[]
     */
    protected function getControllerProviderStack($isSsl)
    {
        return [
            new StorefinderControllerProvider(),
        ];
    }
```

### Symfony Routing
add in src/Pyz/Yves/Router/RouterDependencyProvider.php
```
    /**
     * @return \Spryker\Yves\RouterExtension\Dependency\Plugin\RouteProviderPluginInterface[]
     */
    protected function getRouteProvider(): array
    {
        return [
            ...
            new StorefinderRouteProviderPlugin(),
        ];
    }
```
