<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

interface OfferGuiToStoreFacadeInterface
{
    /**
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getAllStores(): array;

    /**
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function getCurrentStore(): StoreTransfer;
}
