<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class OfferGuiConfig extends AbstractBundleConfig
{
    /**
     * @uses \Spryker\Zed\Offer\OfferConfig::getOrderTypeOffer()
     */
    public const ORDER_TYPE_OFFER = 'offer';

    /**
     * @return string
     */
    public function getOrderTypeOffer(): string
    {
        return static::ORDER_TYPE_OFFER;
    }
}