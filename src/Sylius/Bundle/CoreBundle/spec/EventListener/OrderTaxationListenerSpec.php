<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderTaxationListenerSpec extends ObjectBehavior
{
    function let(OrderTaxesProcessorInterface $orderTaxesProcessor)
    {
        $this->beConstructedWith($orderTaxesProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderTaxationListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringApplyTaxes($event)
        ;
    }

    function it_calls_taxation_processor_on_order(
        OrderTaxesProcessorInterface $orderTaxesProcessor,
        GenericEvent $event,
        OrderInterface $order
    ) {
        $event->getSubject()->willReturn($order);
        $orderTaxesProcessor->apply($order)->shouldBeCalled();

        $this->applyTaxes($event);
    }
}
