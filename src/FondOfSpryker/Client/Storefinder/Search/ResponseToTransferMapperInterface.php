<?php

namespace FondOfSpryker\Client\Storefinder\Search;

use Generated\Shared\Transfer\StorefinderSearchResponseTransfer;

interface ResponseToTransferMapperInterface
{
    /**
     * @param string[] $elasticsearchResponse
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function mapResponseToTransfer(array $elasticsearchResponse): StorefinderSearchResponseTransfer;
}
