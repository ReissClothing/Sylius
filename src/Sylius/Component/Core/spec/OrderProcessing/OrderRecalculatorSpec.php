<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculator;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @mixin OrderRecalculator
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculatorSpec extends ObjectBehavior
{
    function let(
        PromotionProcessorInterface $promotionProcessor,
        OrderTaxesProcessorInterface $orderTaxesProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->beConstructedWith($promotionProcessor, $orderTaxesProcessor, $shippingChargesProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderRecalculator');
    }

    function it_implements_order_recalculator_interface()
    {
        $this->shouldImplement(OrderRecalculatorInterface::class);
    }

    function it_recalculates_order_promotions_taxes_and_shipping_charges(
        PromotionProcessorInterface $promotionProcessor,
        OrderTaxesProcessorInterface $orderTaxesProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor,
        OrderInterface $order
    ) {
        $promotionProcessor->process($order)->shouldBeCalled();
        $orderTaxesProcessor->apply($order)->shouldBeCalled();
        $shippingChargesProcessor->applyShippingCharges($order)->shouldBeCalled();

        $this->recalculate($order);
    }
}
