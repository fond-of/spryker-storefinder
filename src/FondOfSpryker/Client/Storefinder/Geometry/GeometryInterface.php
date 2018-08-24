<?php

namespace FondOfSpryker\Client\Storefinder\Geometry;

use Generated\Shared\Transfer\StorefinderGeometryRequestTransfer;
use Generated\Shared\Transfer\StorefinderGeometryResponseTransfer;

interface GeometryInterface
{
    /**
     * @param \Generated\Shared\Transfer\StorefinderGeometryRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\StorefinderGeometryResponseTransfer|null
     */
    public function findGeometryBy(StorefinderGeometryRequestTransfer $requestTransfer): ?StorefinderGeometryResponseTransfer;
}
