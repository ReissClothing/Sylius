<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\OrderItemUnit');
    }

    function it_implements_order_item_unit_interface()
    {
        $this->shouldImplement(OrderItemUnitInterface::class);
    }

    function it_has_0_total_as_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_adds_and_removes_adjustments(AdjustmentInterface $adjustment)
    {
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $this->removeAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    function it_calculates_its_total(AdjustmentInterface $adjustment1, AdjustmentInterface $adjustment2, OrderItemInterface $orderItem)
    {
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $adjustment1->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(50);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->setOrderItem($orderItem);
        $orderItem->getUnitPrice()->willReturn(1000);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(1150);
    }

    function it_calculates_its_total_properly_after_order_item_unit_price_change(AdjustmentInterface $adjustment1, AdjustmentInterface $adjustment2, OrderItemInterface $orderItem)
    {
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $adjustment1->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(50);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->setOrderItem($orderItem);
        $orderItem->getUnitPrice()->willReturn(1000, 4000);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(1150);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(4150);
    }

    function it_calculates_its_total_properly_after_adjustments_modification(
        AdjustmentInterface $adjustment1,
        AdjustmentInterface $adjustment2,
        AdjustmentInterface $adjustment3,
        OrderItemInterface $orderItem
    ) {
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();
        $adjustment1->isLocked()->willReturn(false);
        $adjustment1->setAdjustable(null)->shouldBeCalled();

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $adjustment3->isNeutral()->willReturn(false);
        $adjustment3->setAdjustable($this)->shouldBeCalled();

        $adjustment1->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(50);
        $adjustment3->getAmount()->willReturn(250);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->setOrderItem($orderItem);
        $orderItem->getUnitPrice()->willReturn(1000);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(1150);

        $this->removeAdjustment($adjustment1);
        $this->addAdjustment($adjustment3);
        $this->calculateTotal();

        $this->getTotal()->shouldReturn(1300);
    }

    function it_calculates_its_total_properly_after_adjustments_clear(AdjustmentInterface $adjustment1, AdjustmentInterface $adjustment2, OrderItemInterface $orderItem)
    {
        $adjustment1->isNeutral()->willReturn(false);
        $adjustment1->setAdjustable($this)->shouldBeCalled();

        $adjustment2->isNeutral()->willReturn(false);
        $adjustment2->setAdjustable($this)->shouldBeCalled();

        $adjustment1->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(50);

        $this->addAdjustment($adjustment1);
        $this->addAdjustment($adjustment2);

        $this->setOrderItem($orderItem);
        $orderItem->getUnitPrice()->willReturn(1000);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(1150);

        $this->clearAdjustments();
        $this->calculateTotal();

        $this->getTotal()->shouldReturn(1000);
    }
}
