<?php

namespace FondOfSpryker\Client\Storefinder;

use Generated\Shared\Transfer\StorefinderCustomerAddressTransfer;
use Generated\Shared\Transfer\StorefinderSearchRequestTransfer;
use Generated\Shared\Transfer\StorefinderSearchResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \FondOfSpryker\Client\Storefinder\StorefinderFactory getFactory()
 */
class StorefinderClient extends AbstractClient implements StorefinderClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\StorefinderSearchRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function search(StorefinderSearchRequestTransfer $requestTransfer): StorefinderSearchResponseTransfer
    {
        return $this->getFactory()->createCustomerAddressRepository()->search($requestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderSearchRequestTransfer $requestTransfer
     *
     * @return null|\Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function findOneByUrlKey(string $urlKey): ?StorefinderCustomerAddressTransfer
    {
        return $this->getFactory()->createCustomerAddressRepository()->findOneByUrlKey($urlKey);
    }
}
