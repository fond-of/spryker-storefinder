<?php

namespace FondOfSpryker\Client\Storefinder;

use Generated\Shared\Transfer\StorefinderCustomerAddressTransfer;
use Generated\Shared\Transfer\StorefinderSearchRequestTransfer;
use Generated\Shared\Transfer\StorefinderSearchResponseTransfer;

interface StorefinderClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\StorefinderSearchRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function search(StorefinderSearchRequestTransfer $requestTransfer): StorefinderSearchResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\StorefinderSearchRequestTransfer $requestTransfer
     *
     * @return null|\Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function findOneByUrlKey(string $urlKey): ?StorefinderCustomerAddressTransfer;
}
