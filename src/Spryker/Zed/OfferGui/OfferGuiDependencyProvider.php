<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui;

use Orm\Zed\Offer\Persistence\SpyOfferQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToCustomerFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToMoneyFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Facade\OfferGuiToOfferFacadeBridge;
use Spryker\Zed\OfferGui\Dependency\Service\OfferGuiToUtilDateTimeServiceBridge;
use Spryker\Zed\OfferGui\Dependency\Service\OfferGuiToUtilSanitizeServiceBridge;

class OfferGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_OFFER = 'FACADE_OFFER';
    public const FACADE_CUSTOMER = 'FACADE_CUSTOMER';
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const SERVICE_UTIL_DATE_TIME = 'SERVICE_UTIL_DATE_TIME';
    public const SERVICE_UTIL_SANITIZE = 'SERVICE_UTIL_SANITIZE';
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
        $container = $this->addCustomerFacade($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addUtilDateTimeService($container);
        $container = $this->addUtilSanitize($container);
        $container = $this->addPropelQueryOffer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCustomerFacade(Container $container)
    {
        $container[static::FACADE_CUSTOMER] = function (Container $container) {
            return new OfferGuiToCustomerFacadeBridge($container->getLocator()->customer()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container)
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return new OfferGuiToMoneyFacadeBridge($container->getLocator()->money()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilDateTimeService(Container $container)
    {
        $container[static::SERVICE_UTIL_DATE_TIME] = function (Container $container) {
            return new OfferGuiToUtilDateTimeServiceBridge($container->getLocator()->utilDateTime()->service());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilSanitize(Container $container)
    {
        $container[static::SERVICE_UTIL_SANITIZE] = function (Container $container) {
            return new OfferGuiToUtilSanitizeServiceBridge($container->getLocator()->utilSanitize()->service());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPropelQueryOffer(Container $container)
    {
        $container[static::PROPEL_QUERY_OFFER] = function (Container $container) {
            return SpyOfferQuery::create();
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addOfferFacade(Container $container)
    {
        $container[static::FACADE_OFFER] = function (Container $container) {
            return new OfferGuiToOfferFacadeBridge($container->getLocator()->offer()->facade());
        };

        return $container;
    }
}
