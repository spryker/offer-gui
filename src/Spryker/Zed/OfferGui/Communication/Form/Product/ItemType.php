<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\OfferGui\Communication\Form\Product;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @method \Spryker\Zed\OfferGui\Communication\ManualOrderEntryGuiCommunicationFactory getFactory()
 */
class ItemType extends AbstractType
{
    public const FIELD_SKU = 'sku';
    public const FIELD_QUANTITY = 'quantity';
    public const FIELD_UNIT_GROSS_PRICE = 'unitGrossPrice';
    public const FIELD_FORCED_UNIT_GROSS_PRICE = 'forcedUnitGrossPrice';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                $this->getFactory()->createSkuExistsConstraint(),
            ],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addSkuField($builder, $options)
            ->addQuantityField($builder, $options)
            ->addUnitGrossPriceField($builder, $options)
            ->addForcedUnitGrossPriceField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addSkuField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SKU, TextType::class, [
            'label' => 'SKU',
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addUnitGrossPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_UNIT_GROSS_PRICE, TextType::class, [
            'label' => 'Unit Gross Price',
            'required' => false,
            'constraints' => [
                $this->createNumberConstraint($options),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
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
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addForcedUnitGrossPriceField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_FORCED_UNIT_GROSS_PRICE, HiddenType::class, [
            'data' => 1,
        ]);

        return $this;
    }

    /**
     * @param array $options
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
     * @param array $options
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
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'product';
    }
}