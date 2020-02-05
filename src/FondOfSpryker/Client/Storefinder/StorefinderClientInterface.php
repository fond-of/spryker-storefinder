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
     * @param string $urlKey
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer|null
     */
    public function findOneByUrlKey(string $urlKey): ?StorefinderCustomerAddressTransfer;
}
