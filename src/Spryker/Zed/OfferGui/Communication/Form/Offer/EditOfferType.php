<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Communication\Form\Offer;

use DateTime;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\Gui\Communication\Form\Type\FormattedNumberType;
use Spryker\Zed\Gui\Communication\Form\Type\Select2ComboBoxType;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\OfferGui\Communication\Form\Address\AddressType;
use Spryker\Zed\OfferGui\Communication\Form\Item\IncomingItemType;
use Spryker\Zed\OfferGui\Communication\Form\Item\ItemType;
use Spryker\Zed\OfferGui\Communication\Form\Voucher\VoucherType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @method \Spryker\Zed\OfferGui\OfferGuiConfig getConfig()
 * @method \Spryker\Zed\OfferGui\Communication\OfferGuiCommunicationFactory getFactory()
 */
class EditOfferType extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_ID_OFFER = 'idOffer';

    /**
     * @var string
     */
    public const FIELD_STORE_NAME = 'storeName';

    /**
     * @var string
     */
    public const FIELD_CURRENCY_CODE = 'currencyCode';

    /**
     * @var string
     */
    public const FIELD_STORE_CURRENCY = 'storeCurrency';

    /**
     * @var string
     */
    public const FIELD_ITEMS = 'items';

    /**
     * @var string
     */
    public const FIELD_INCOMING_ITEMS = 'incomingItems';

    /**
     * @var string
     */
    public const FIELD_VOUCHER_DISCOUNTS = 'voucherDiscounts';

    /**
     * @var string
     */
    public const FIELD_CUSTOMER_REFERENCE = 'customerReference';

    /**
     * @var string
     */
    public const FIELD_QUOTE_SHIPPING_ADDRESS = 'shippingAddress';

    /**
     * @var string
     */
    public const FIELD_QUOTE_BILLING_ADDRESS = 'billingAddress';

    /**
     * @var string
     */
    public const FIELD_OFFER_FEE = 'offerFee';

    /**
     * @var string
     */
    public const FIELD_CONTACT_PERSON = 'contactPerson';

    /**
     * @var string
     */
    public const FIELD_CONTACT_DATE = 'contactDate';

    /**
     * @var string
     */
    public const FIELD_NOTE = 'note';

    /**
     * @var string
     */
    public const FIELD_OFFER_STATUS = 'status';

    /**
     * @var string
     */
    public const OPTION_CUSTOMER_LIST = 'option-customer-list';

    /**
     * @var string
     */
    public const OPTION_STORE_CURRENCY_LIST = 'option-store-currency-list';

    /**
     * @var string
     */
    public const OPTION_OFFER_STATUS_LIST = 'option-offer-status-list';

    /**
     * @var string
     */
    public const OPTION_LOCALE = 'locale';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PRICE = 'Invalid Price.';

    /**
     * @var string
     */
    protected const PATTERN_MONEY = '/^\d*\.?\d{0,2}$/';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(static::OPTION_CUSTOMER_LIST)
            ->setRequired(static::OPTION_OFFER_STATUS_LIST)
            ->setRequired(static::OPTION_STORE_CURRENCY_LIST);

        $resolver->setDefaults([
            static::OPTION_LOCALE => null,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addIdOfferField($builder)
            ->addStatusOfferList($builder, $options)
            ->addStoreNameField($builder)
            ->addCurrencyCodeField($builder)
            ->addStoreCurrencyField($builder, $options)
            ->addCustomerChoiceField($builder, $options)
            ->addShippingAddressField($builder, $options)
            ->addBillingAddressField($builder, $options)
            ->addItemsField($builder, $options)
            ->addIncomingItemsField($builder)
            ->addVoucherDiscountsField($builder, $options)
            ->addOfferFeeField($builder, $options)
            ->addContactPersonField($builder)
            ->addContactDateField($builder)
            ->addNoteField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addStoreNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_STORE_NAME, HiddenType::class, [
            'property_path' => 'quote.store.name',
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addStatusOfferList(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            /** @var \Generated\Shared\Transfer\OfferTransfer $offerTransfer */
            $offerTransfer = $event->getData();
            if ($offerTransfer->getIdOffer() === null) {
                return;
            }

            $offerStatusList = $options[static::OPTION_OFFER_STATUS_LIST];

            $form = $event->getForm();
            $form->add(static::FIELD_OFFER_STATUS, Select2ComboBoxType::class, [
                'label' => 'Select State',
                'choices' => array_combine($offerStatusList, $offerStatusList),
                'multiple' => false,
                'constraints' => [
                    new NotBlank(),
                ],
                'required' => true,
            ]);
        });

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCurrencyCodeField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CURRENCY_CODE, HiddenType::class, [
            'property_path' => 'quote.currency.code',
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addStoreCurrencyField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_STORE_CURRENCY, Select2ComboBoxType::class, [
            'label' => 'Store/Currency',
            'required' => true,
            'choices' => $options[static::OPTION_STORE_CURRENCY_LIST],
            'multiple' => false,
            'mapped' => false,
            'data' => $this->getSelectedStoreCurrency($builder),
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event): void {
                $data = $event->getData();

                $storeCurrency = $data[static::FIELD_STORE_CURRENCY];
                [$storeName, $currencyCode] = $this->getStoreAndCurrency($storeCurrency);

                $data[static::FIELD_STORE_NAME] = $storeName;
                $data[static::FIELD_CURRENCY_CODE] = $currencyCode;
                $event->setData($data);
            },
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdOfferField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_OFFER, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addCustomerChoiceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_CUSTOMER_REFERENCE, Select2ComboBoxType::class, [
            'label' => 'Select Customer',
            'choices' => array_flip($options[static::OPTION_CUSTOMER_LIST]),
            'multiple' => false,
            'constraints' => [
                new NotBlank(),
            ],
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addShippingAddressField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_QUOTE_SHIPPING_ADDRESS, AddressType::class, [
            'property_path' => 'quote.shippingAddress',
            'label' => 'Shipping address',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addBillingAddressField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_QUOTE_BILLING_ADDRESS, AddressType::class, [
            'property_path' => 'quote.billingAddress',
            'label' => 'Billing address',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addItemsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_ITEMS, CollectionType::class, [
            'entry_type' => ItemType::class,
            'property_path' => 'quote.items',
            'label' => 'Selected products',
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => [
                'label' => false,
                'data_class' => ItemTransfer::class,
                'locale' => $options[static::OPTION_LOCALE],
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIncomingItemsField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_INCOMING_ITEMS, CollectionType::class, [
            'entry_type' => IncomingItemType::class,
            'property_path' => 'quote.incomingItems',
            'label' => 'Select products',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => [
                'label' => false,
                'data_class' => ItemTransfer::class,
            ],
            'constraints' => [
                new Callback(function ($items, ExecutionContextInterface $context): void {
                    /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
                    foreach ($items as $itemTransfer) {
                        if ($itemTransfer->getSku() && !$itemTransfer->getQuantity()) {
                            $context->buildViolation('One of selected products contains invalid quantity')
                                ->addViolation();

                            break;
                        }
                    }
                }),
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
    protected function addVoucherDiscountsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_VOUCHER_DISCOUNTS, CollectionType::class, [
            'label' => false,
            'entry_type' => VoucherType::class,
            'property_path' => 'quote.voucherDiscounts',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'entry_options' => [
                'label' => false,
                'data_class' => DiscountTransfer::class,
                'locale' => $options[static::OPTION_LOCALE],
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
            'property_path' => 'quote.offerFee',
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
     *
     * @return $this
     */
    protected function addContactPersonField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTACT_PERSON, TextType::class, [
            'label' => 'Person in charge',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactDateField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTACT_DATE, DateType::class, [
            'label' => 'Next contact date',
            'widget' => 'single_text',
            'required' => false,
            'attr' => [
                'class' => 'datepicker safe-datetime',
            ],
        ]);

        $builder->get(static::FIELD_CONTACT_DATE)
            ->addModelTransformer($this->createDateTimeModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNoteField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_NOTE, TextareaType::class, [
            'label' => 'Comment',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return string
     */
    private function getSelectedStoreCurrency(FormBuilderInterface $builder): string
    {
        /** @var \Generated\Shared\Transfer\OfferTransfer $offerTransfer */
        $offerTransfer = $builder->getData();
        $quoteTransfer = $offerTransfer->getQuote();
        $storeName = $quoteTransfer->getStore()->getName();
        $currencyCode = $quoteTransfer->getCurrency()->getCode();

        return implode(';', [$storeName, $currencyCode]);
    }

    /**
     * @param string $storeCurrency
     *
     * @return array<string>
     */
    private function getStoreAndCurrency(string $storeCurrency): array
    {
        return explode(';', $storeCurrency);
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createMoneyConstraint(array $options): Regex
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
    protected function getValidationGroup(array $options): string
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
    protected function createMoneyModelTransformer(): CallbackTransformer
    {
        return new CallbackTransformer(
            function ($value) {
                if ($value !== null) {
                    return $value / 100;
                }
            },
            function ($value) {
                return (int)($value * 100);
            },
        );
    }

    /**
     * @return \Symfony\Component\Form\CallbackTransformer
     */
    protected function createDateTimeModelTransformer(): CallbackTransformer
    {
        return new CallbackTransformer(
            function ($value) {
                if ($value !== null) {
                    return new DateTime($value);
                }

                return $value;
            },
            function ($value) {
                if ($value instanceof DateTime) {
                    return $value->format('c');
                }

                return $value;
            },
        );
    }
}
