<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Dependency\Facade;

interface OfferGuiToOmsFacadeInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return array<array<string>>
     */
    public function getManualEventsByIdSalesOrder(int $idSalesOrder): array;

    /**
     * @param int $idSalesOrder
     *
     * @return array<string>
     */
    public function getDistinctManualEventsByIdSalesOrder(int $idSalesOrder): array;
}
