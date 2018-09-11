<?php

namespace FondOfSpryker\Yves\Storefinder;

use Spryker\Client\GlossaryStorage\GlossaryStorageClient;
use Spryker\Yves\Kernel\AbstractFactory;

class StorefinderFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\GlossaryStorage\GlossaryStorageClient
     */
    public function getGlossaryStorageClient(): GlossaryStorageClient
    {
        return $this->getProvidedDependency(StorefinderDependencyProvider::CLIENT_GLOSSARY_STORAGE);
    }
}
