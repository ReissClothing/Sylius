<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculator implements OrderRecalculatorInterface
{
    /**
     * @var PromotionProcessorInterface
     */
    private $promotionProcessor;

    /**
     * @var OrderTaxesProcessorInterface
     */
    private $orderTaxesProcessor;

    /**
     * @var ShippingChargesProcessorInterface
     */
    private $shippingChargesProcessor;

    /**
     * @param PromotionProcessorInterface $promotionProcessor
     * @param OrderTaxesProcessorInterface $orderTaxesProcessor
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
     */
    public function __construct(
        PromotionProcessorInterface $promotionProcessor,
        OrderTaxesProcessorInterface $orderTaxesProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->promotionProcessor = $promotionProcessor;
        $this->orderTaxesProcessor = $orderTaxesProcessor;
        $this->shippingChargesProcessor = $shippingChargesProcessor;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function recalculate(OrderInterface $order)
    {
        $this->promotionProcessor->process($order);
        $this->orderTaxesProcessor->apply($order);
        $this->shippingChargesProcessor->applyShippingCharges($order);
    }
}
