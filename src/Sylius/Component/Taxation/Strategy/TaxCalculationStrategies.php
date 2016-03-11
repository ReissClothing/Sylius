<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Strategy;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class TaxCalculationStrategies
{
    // Order items based tax calculation strategy.
    const ORDER_ITEMS_BASED = 'order_items_based';

    // Order item units based tax calculation strategy.
    const ORDER_ITEM_UNITS_BASED = 'order_item_units_based';
}
