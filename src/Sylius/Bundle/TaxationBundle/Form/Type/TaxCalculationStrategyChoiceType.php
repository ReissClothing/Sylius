<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Form\Type;

use Sylius\Component\Taxation\Strategy\TaxCalculationStrategies;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class TaxCalculationStrategyChoiceType extends AbstractType
{
    protected $strategies = [
        TaxCalculationStrategies::ORDER_ITEMS_BASED => 'sylius.form.tax_calculation_strategy.strategies.order_items',
        TaxCalculationStrategies::ORDER_ITEM_UNITS_BASED => 'sylius.form.tax_calculation_strategy.strategies.order_item_units',
    ];

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => $this->strategies,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_tax_calculation_strategy_choice';
    }
}
