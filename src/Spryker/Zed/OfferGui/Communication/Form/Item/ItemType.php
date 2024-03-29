<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Communication\Form\Item;

use Spryker\Zed\Gui\Communication\Form\Type\FormattedNumberType;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @method \Spryker\Zed\OfferGui\Communication\OfferGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\OfferGui\OfferGuiConfig getConfig()
 */
class ItemType extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_SKU = 'sku';

    /**
     * @var string
     */
    public const FIELD_GROUP_KEY = 'groupKey';

    /**
     * @var string
     */
    public const FIELD_QUANTITY = 'quantity';

    /**
     * @var string
     */
    public const FIELD_OFFER_FEE = 'offerFee';

    /**
     * @var string
     */
    public const FIELD_STOCK = 'stock';

    /**
     * @var string
     */
    public const FIELD_UNIT_GROSS_PRICE = 'unitGrossPrice';

    /**
     * @var string
     */
    public const FIELD_UNIT_NET_PRICE = 'unitNetPrice';

    /**
     * @var string
     */
    public const FIELD_SOURCE_UNIT_GROSS_PRICE = 'sourceUnitGrossPrice';

    /**
     * @var string
     */
    public const FIELD_SOURCE_UNIT_NET_PRICE = 'sourceUnitNetPrice';

    /**
     * @var string
     */
    public const FIELD_OFFER_DISCOUNT = 'offerDiscount';

    /**
     * @var string
     */
    public const FIELD_UNIT_SUBTOTAL_AGGREGATION = 'unitSubtotalAggregation';

    /**
     * @var string
     */
    public const FIELD_SUM_SUBTOTAL_AGGREGATION = 'sumSubtotalAggregation';

    /**
     * @var string
     */
    public const FIELD_FORCED_UNIT_GROSS_PRICE = 'forcedUnitGrossPrice';

    /**
     * @var string
     */
    public const FIELD_FORCED_UNIT_NET_PRICE = 'forcedUnitNetPrice';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PRICE = 'Invalid Price.';

    /**
     * @var string
     */
    protected const PATTERN_MONEY = '/^\d*\.?\d{0,2}$/';

    /**
     * @var string
     */
    protected const OPTION_LOCALE = 'locale';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addSkuField($builder, $options)
            ->addManualUnitPriceField($builder, $options)
            ->addUnitPriceField($builder, $options)
            ->addOfferDiscountField($builder, $options)
            ->addOfferFeeField($builder, $options)
            ->addStockField($builder, $options)
            ->addQuantityField($builder, $options)
            ->addUnitSubtotalAggregationPriceField($builder, $options)
            ->addSumSubtotalAggregationPriceField($builder, $options);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            static::OPTION_LOCALE => null,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addSkuField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SKU, TextType::class, [
            'label' => 'SKU',
            'required' => true,
            'attr' => [
                'readonly' => true,
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addGroupKeyField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_GROUP_KEY, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addUnitPriceField(FormBuilderInterface $builder, array $options)
    {
        if ($this->isDefaultPriceModeGross()) {
            $this->addUnitGrossPriceField($builder, $options);

            return $this;
        }

        $this->addUnitNetPriceField($builder, $options);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addManualUnitPriceField(FormBuilderInterface $builder, array $options)
    {
        if ($this->isDefaultPriceModeGross()) {
            $this->addManualGrossPriceField($builder, $options);

            return $this;
        }

        $this->addManualNetPriceField($builder, $options);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addUnitGrossPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_UNIT_GROSS_PRICE, FormattedNumberType::class, [
            'label' => 'Gross Price',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'disabled' => true,
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_UNIT_GROSS_PRICE)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addUnitNetPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_UNIT_NET_PRICE, FormattedNumberType::class, [
            'label' => 'Net Price',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'disabled' => true,
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_UNIT_NET_PRICE)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addManualGrossPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SOURCE_UNIT_GROSS_PRICE, FormattedNumberType::class, [
            'label' => 'Manual Gross Price',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'disabled' => !$this->isDefaultPriceModeGross(),
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_SOURCE_UNIT_GROSS_PRICE)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addManualNetPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SOURCE_UNIT_NET_PRICE, FormattedNumberType::class, [
            'label' => 'Manual Net Price',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'disabled' => !$this->isDefaultPriceModeNet(),
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_SOURCE_UNIT_NET_PRICE)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addOfferDiscountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_OFFER_DISCOUNT, FormattedNumberType::class, [
            'label' => 'Offer discount %',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'constraints' => [
                new Range([
                    'min' => 0,
                    'max' => 100,
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addStockField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_STOCK, TextType::class, [
            'label' => 'Stock',
            'required' => false,
            'disabled' => true,
            'constraints' => [
                $this->createNumberConstraint($options),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addOfferFeeField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_OFFER_FEE, FormattedNumberType::class, [
            'label' => 'Offer fee',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_OFFER_FEE)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addUnitSubtotalAggregationPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_UNIT_SUBTOTAL_AGGREGATION, FormattedNumberType::class, [
            'label' => 'Unit Subtotal Price',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'disabled' => true,
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_UNIT_SUBTOTAL_AGGREGATION)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addSumSubtotalAggregationPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SUM_SUBTOTAL_AGGREGATION, FormattedNumberType::class, [
            'label' => 'Sum Subtotal Price',
            'locale' => $options[static::OPTION_LOCALE],
            'required' => false,
            'disabled' => true,
            'constraints' => [
                $this->createMoneyConstraint($options),
            ],
        ]);

        $builder
            ->get(static::FIELD_SUM_SUBTOTAL_AGGREGATION)
            ->addModelTransformer($this->createMoneyModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addQuantityField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_QUANTITY, TextType::class, [
            'label' => 'Quantity',
            'required' => false,
            'constraints' => [
                $this->createNumberConstraint($options),
            ],
        ]);

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createNumberConstraint(array $options)
    {
        $validationGroup = $this->getValidationGroup($options);

        return new Regex([
            'pattern' => '/^\d*$/',
            'message' => 'This field should contain digits.',
            'groups' => $validationGroup,
        ]);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createMoneyConstraint(array $options)
    {
        $validationGroup = $this->getValidationGroup($options);

        return new Regex([
            'pattern' => static::PATTERN_MONEY,
            'message' => static::ERROR_MESSAGE_PRICE,
            'groups' => $validationGroup,
        ]);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return string
     */
    protected function getValidationGroup(array $options)
    {
        $validationGroup = Constraint::DEFAULT_GROUP;
        if (!empty($options['validation_group'])) {
            $validationGroup = $options['validation_group'];
        }

        return $validationGroup;
    }

    /**
     * @return \Symfony\Component\Form\CallbackTransformer
     */
    protected function createMoneyModelTransformer()
    {
        return new CallbackTransformer(
            function ($value) {
                if ($value !== null) {
                    return $value / 100;
                }
            },
            function ($value) {
                if ($value !== null) {
                    return $value * 100;
                }
            },
        );
    }

    /**
     * @return bool
     */
    protected function isDefaultPriceModeNet()
    {
        return $this->getFactory()->getPriceFacade()->getDefaultPriceMode() === $this->getConfig()->getPriceModeNet();
    }

    /**
     * @return bool
     */
    protected function isDefaultPriceModeGross()
    {
        return $this->getFactory()->getPriceFacade()->getDefaultPriceMode() === $this->getConfig()->getPriceModeGross();
    }
}
