<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Operator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryOperatorSpec extends ObjectBehavior
{
    function let(
        BackordersHandlerInterface $backordersHandler,
        AvailabilityCheckerInterface $availabilityChecker,
        EventDispatcher $eventDispatcher
    ) {
        $this->beConstructedWith($backordersHandler, $availabilityChecker, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Operator\InventoryOperator');
    }

    function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement(InventoryOperatorInterface::class);
    }

    function it_increases_stockable_on_hand(StockableInterface $stockable)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(2);
        $stockable->setOnHand(7)->shouldBeCalled();

        $this->increase($stockable, 5);
    }

    function it_decreases_stockable_on_hand(StockableInterface $stockable)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(5);
        $stockable->setOnHand(2)->shouldBeCalled();

        $this->decrease($stockable, 3);
    }
}
