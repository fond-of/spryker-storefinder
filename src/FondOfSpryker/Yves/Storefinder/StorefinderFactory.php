<?php

namespace FondOfSpryker\Yves\Storefinder;

use Collator;
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

    /**
     * @return \FondOfSpryker\Yves\Storefinder\StorefinderConfig
     */
    public function getStorefinderConfig(): StorefinderConfig
    {
        return $this->getConfig();
    }

    /**
     * @return \Collator
     */
    public function createCollator(string $locale): Collator
    {
        return new Collator($locale);
    }
}
