<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Dependency\Facade;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\OfferResponseTransfer;
use Generated\Shared\Transfer\OfferTransfer;

class OfferGuiToOfferFacadeBridge implements OfferGuiToOfferFacadeInterface
{
    /**
     * @var \Spryker\Zed\Offer\Business\OfferFacadeInterface
     */
    protected $offerFacade;

    /**
     * @param \Spryker\Zed\Offer\Business\OfferFacadeInterface $offerFacade
     */
    public function __construct($offerFacade)
    {
        $this->offerFacade = $offerFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return \Generated\Shared\Transfer\OfferTransfer
     */
    public function getOfferById(OfferTransfer $offerTransfer): OfferTransfer
    {
        return $this->offerFacade->getOfferById($offerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return \Generated\Shared\Transfer\OfferResponseTransfer
     */
    public function updateOffer(OfferTransfer $offerTransfer): OfferResponseTransfer
    {
        return $this->offerFacade->updateOffer($offerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return \Generated\Shared\Transfer\OfferResponseTransfer
     */
    public function createOffer(OfferTransfer $offerTransfer): OfferResponseTransfer
    {
        return $this->offerFacade->createOffer($offerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function aggregateOfferItemSubtotal(CalculableObjectTransfer $calculableObjectTransfer): void
    {
        $this->offerFacade->aggregateOfferItemSubtotal($calculableObjectTransfer);
    }
}
