<?php

namespace Pyz\Yves\Storefinder\Plugin\Provider;

use Pyz\Yves\Application\Plugin\Provider\AbstractYvesControllerProvider;
use Silex\Application;

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
        $this->createGetController('/storefinder', static::STOREFINDER_INDEX, 'Storefinder', 'Index', 'index');
    }

}
