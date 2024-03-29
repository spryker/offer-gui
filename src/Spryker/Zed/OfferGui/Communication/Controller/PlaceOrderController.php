<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Communication\Controller;

use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\OfferGui\Communication\Plugin\ManualOrderEntryGui\OfferQuoteExpanderPlugin;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\OfferGui\Communication\OfferGuiCommunicationFactory getFactory()
 */
class PlaceOrderController extends AbstractController
{
    /**
     * @var string
     */
    public const PARAM_ID_OFFER = 'id-offer';

    /**
     * @var string
     */
    public const URL_ORDER_ENTRY = '/manual-order-entry-gui/create';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idOffer = $request->get(static::PARAM_ID_OFFER);

        $redirectUrl = Url::generate(
            static::URL_ORDER_ENTRY,
            [OfferQuoteExpanderPlugin::PARAM_ID_OFFER => $idOffer],
        )->build();

        return $this->redirectResponse($redirectUrl);
    }
}
