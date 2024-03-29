<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui;

use Orm\Zed\Offer\Persistence\SpyOfferQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\OfferGui\Dependency\Client\OfferGuiToSessionClientBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToCartFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToCurrencyFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToCustomerFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToLocaleFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToMessengerFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToMoneyFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToOfferFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToOmsFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToPriceFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToStoreFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Service\OfferGuiToUtilDateTimeServiceBridge;
use Spryker\Zed\OfferGui\Dependency\Service\OfferGuiToUtilEncodingServiceBridge;
use Spryker\Zed\OfferGui\Dependency\Service\OfferGuiToUtilSanitizeServiceBridge;

/**
 * @method \Spryker\Zed\OfferGui\OfferGuiConfig getConfig()
 */
class OfferGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_OFFER = 'FACADE_OFFER';

    /**
     * @var string
     */
    public const FACADE_CUSTOMER = 'FACADE_CUSTOMER';

    /**
     * @var string
     */
    public const FACADE_MONEY = 'FACADE_MONEY';

    /**
     * @var string
     */
    public const FACADE_OMS = 'FACADE_OMS';

    /**
     * @var string
     */
    public const FACADE_CART = 'FACADE_CART';

    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const FACADE_MESSENGER = 'FACADE_MESSENGER';

    /**
     * @var string
     */
    public const FACADE_CURRENCY = 'FACADE_CURRENCY';

    /**
     * @var string
     */
    public const FACADE_PRICE = 'FACADE_PRICE';

    /**
     * @var string
     */
    public const FACADE_LOCALE = 'FACADE_LOCALE';

    /**
     * @var string
     */
    public const CLIENT_SESSION = 'CLIENT_SESSION';

    /**
     * @var string
     */
    public const SERVICE_UTIL_DATE_TIME = 'SERVICE_UTIL_DATE_TIME';

    /**
     * @var string
     */
    public const SERVICE_UTIL_SANITIZE = 'SERVICE_UTIL_SANITIZE';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const PROPEL_QUERY_OFFER = 'PROPEL_QUERY_OFFER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addOfferFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addCustomerFacade($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addOmsFacade($container);
        $container = $this->addCartFacade($container);
        $container = $this->addUtilDateTimeService($container);
        $container = $this->addUtilSanitize($container);
        $container = $this->addUtilEncoding($container);
        $container = $this->addPropelQueryOffer($container);
        $container = $this->addMessengerFacade($container);
        $container = $this->addSessionClient($container);
        $container = $this->addCurrencyFacade($container);
        $container = $this->addPriceFacade($container);
        $container = $this->addLocaleFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPriceFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRICE, function (Container $container) {
            return new OfferGuiToPriceFacadeBridge($container->getLocator()->price()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCustomerFacade(Container $container): Container
    {
        $container->set(static::FACADE_CUSTOMER, function (Container $container) {
            return new OfferGuiToCustomerFacadeBridge($container->getLocator()->customer()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCurrencyFacade(Container $container): Container
    {
        $container->set(static::FACADE_CURRENCY, function (Container $container) {
            return new OfferGuiToCurrencyFacadeBridge($container->getLocator()->currency()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSessionClient(Container $container): Container
    {
        $container->set(static::CLIENT_SESSION, function (Container $container) {
            return new OfferGuiToSessionClientBridge($container->getLocator()->session()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMessengerFacade(Container $container): Container
    {
        $container->set(static::FACADE_MESSENGER, function (Container $container) {
            return new OfferGuiToMessengerFacadeBridge($container->getLocator()->messenger()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new OfferGuiToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container): Container
    {
        $container->set(static::FACADE_MONEY, function (Container $container) {
            return new OfferGuiToMoneyFacadeBridge($container->getLocator()->money()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container->set(static::FACADE_OMS, function (Container $container) {
            return new OfferGuiToOmsFacadeBridge($container->getLocator()->oms()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCartFacade(Container $container): Container
    {
        $container->set(static::FACADE_CART, function (Container $container) {
            return new OfferGuiToCartFacadeBridge($container->getLocator()->cart()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilDateTimeService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_DATE_TIME, function (Container $container) {
            return new OfferGuiToUtilDateTimeServiceBridge($container->getLocator()->utilDateTime()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilSanitize(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_SANITIZE, function (Container $container) {
            return new OfferGuiToUtilSanitizeServiceBridge($container->getLocator()->utilSanitize()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilEncoding(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new OfferGuiToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPropelQueryOffer(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_OFFER, $container->factory(function () {
            return SpyOfferQuery::create();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOfferFacade(Container $container): Container
    {
        $container->set(static::FACADE_OFFER, function (Container $container) {
            return new OfferGuiToOfferFacadeBridge($container->getLocator()->offer()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container): Container
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new OfferGuiToLocaleFacadeBridge($container->getLocator()->locale()->facade());
        });

        return $container;
    }
}
