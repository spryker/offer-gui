<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Communication\Controller;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OfferResponseTransfer;
use Generated\Shared\Transfer\OfferTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\OfferGui\Communication\OfferGuiCommunicationFactory getFactory()
 */
class CreateController extends AbstractController
{
    //If this parameter exist, we create new offer using another one
    /**
     * @var string
     */
    public const PARAM_ID_OFFER = 'id-offer';

    /**
     * @var string
     */
    protected const MESSAGE_OFFER_CREATE_SUCCESS = 'Offer was created successfully.';

    /**
     * @var string
     */
    public const PARAM_KEY_INITIAL_OFFER = 'key-offer';

    /**
     * @var string
     */
    public const PARAM_SUBMIT_PERSIST = 'submit-persist';

    /**
     * @var string
     */
    public const PARAM_SUBMIT_CUSTOMER_CREATE = 'submit-customer-create';

    /**
     * @var string
     */
    public const PARAM_SUBMIT_RELOAD = 'submit-reload';

    /**
     * @var string
     */
    public const PARAM_CUSTOMER_REFERENCE = 'customerReference';

    /**
     * @var string
     */
    public const PARAM_KEY_REDIRECT_URL = 'redirectUrl';

    /**
     * @var string
     */
    public const REDIRECT_URL_OFFER_VIEW = '/offer-gui/view/details';

    /**
     * @var string
     */
    protected const SESSION_KEY_OFFER_DATA = 'key-offer-data';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_ITEMS_NOT_AVAILABLE = 'Please fill offer with available items';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $isSubmitPersist = $request->request->get(static::PARAM_SUBMIT_PERSIST);

        $offerTransfer = $this->getOfferTransfer($request);
        //When we create customer, this method restores offer data from session.
        $offerTransfer = $this->processCustomerRedirect($request, $offerTransfer);

        $form = $this->getFactory()->getOfferForm($offerTransfer, $request);
        $form->handleRequest($request);

        if ($request->request->has(static::PARAM_SUBMIT_CUSTOMER_CREATE)) {
            $this->getFactory()
                ->createCreateRequestHandler()
                ->addItems($offerTransfer);

            $redirectBackUrl = $this->storeFormDataIntoSession($form->getData());

            $redirectUrl = Url::generate(
                '/customer/add',
                [static::PARAM_KEY_REDIRECT_URL => urlencode($redirectBackUrl)],
            )->build();

            return $this->redirectResponse($redirectUrl);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Generated\Shared\Transfer\OfferTransfer $offerTransfer */
            $offerTransfer = $form->getData();

            $offerTransfer = $this->getFactory()
                ->getOfferFacade()
                ->calculateOffer($offerTransfer);

            $form = $this->getFactory()->getOfferForm($offerTransfer, $request);

            if ($isSubmitPersist) {
                $offerResponseTransfer = $this->getFactory()
                    ->getOfferFacade()
                    ->createOffer($offerTransfer);

                if ($offerResponseTransfer->getIsSuccessful()) {
                    $this->addSuccessMessage(static::MESSAGE_OFFER_CREATE_SUCCESS);

                    return $this->getSuccessfulRedirect($offerResponseTransfer);
                }
            }
        }

        return $this->viewResponse([
            'offer' => $offerTransfer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return string
     */
    protected function storeFormDataIntoSession(OfferTransfer $offerTransfer): string
    {
        $offerJsonData = $this->getFactory()->getUtilEncoding()->encodeJson($offerTransfer->toArray());
        $offerKey = $this->generateOfferKey($offerJsonData);

        $this->getFactory()
            ->getSessionClient()
            ->set($offerKey, $offerJsonData);

        $redirectUrl = Url::generate(
            '/offer-gui/create',
            [static::PARAM_KEY_INITIAL_OFFER => $offerKey],
        )->build();

        return $redirectUrl;
    }

    /**
     * @param string $offerKey
     *
     * @return array|null
     */
    protected function retrieveFormDataFromSession(string $offerKey): ?array
    {
        $jsonData = $this->getFactory()
            ->getSessionClient()
            ->get($offerKey);

        return $this->getFactory()
            ->getUtilEncoding()
            ->decodeJson($jsonData, true);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\OfferTransfer
     */
    protected function getOfferTransfer(Request $request)
    {
        $idOffer = $request->get(static::PARAM_ID_OFFER);

        if ($idOffer === null) {
            return new OfferTransfer();
        }

        $offerTransfer = (new OfferTransfer())->setIdOffer($idOffer);
        $offerTransfer = $this->getFactory()
            ->getOfferFacade()
            ->getOfferById($offerTransfer);

        $this->cleanupOfferForSession($offerTransfer);

        return $offerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return \Generated\Shared\Transfer\OfferTransfer
     */
    protected function cleanupOfferForSession(OfferTransfer $offerTransfer)
    {
        $offerTransfer->setIdOffer(null)
            ->setCustomerReference(null)
            ->setCustomer(new CustomerTransfer());

        return $offerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OfferResponseTransfer $offerResponseTransfer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function getSuccessfulRedirect(OfferResponseTransfer $offerResponseTransfer)
    {
        $this->getFactory()->createFlashMessageCleaner()->clearDuplicateMessages();

        $redirectUrl = Url::generate(
            static::REDIRECT_URL_OFFER_VIEW,
            [EditController::PARAM_ID_OFFER => $offerResponseTransfer->getOffer()->getIdOffer()],
        )->build();

        return $this->redirectResponse($redirectUrl);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\OfferTransfer $offerTransfer
     *
     * @return \Generated\Shared\Transfer\OfferTransfer
     */
    protected function processCustomerRedirect(Request $request, OfferTransfer $offerTransfer): OfferTransfer
    {
        if (!$request->query->has(static::PARAM_CUSTOMER_REFERENCE) || !$request->query->has(static::PARAM_KEY_INITIAL_OFFER)) {
            return $offerTransfer;
        }
        $offerKey = (string)$request->query->get(static::PARAM_KEY_INITIAL_OFFER);

        $data = $this->retrieveFormDataFromSession($offerKey);

        if (!$data) {
            return $offerTransfer;
        }

        return (new OfferTransfer())->fromArray(
            $data,
        );
    }

    /**
     * @param string $offerJsonData
     *
     * @return string
     */
    protected function generateOfferKey(string $offerJsonData): string
    {
        return md5($offerJsonData);
    }
}
