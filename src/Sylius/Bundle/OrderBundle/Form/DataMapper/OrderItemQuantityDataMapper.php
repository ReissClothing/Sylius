<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Form\DataMapper;

use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\DataMapperInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemQuantityDataMapper implements DataMapperInterface
{
    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;

    /**
     * @var DataMapperInterface
     */
    private $propertyPathDataMapper;

    /**
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     * @param DataMapperInterface $propertyPathDataMapper
     */
    public function __construct(OrderItemQuantityModifierInterface $orderItemQuantityModifier, DataMapperInterface $propertyPathDataMapper)
    {
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->propertyPathDataMapper = $propertyPathDataMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms)
    {
        $this->propertyPathDataMapper->mapDataToForms($data, $forms);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data)
    {
        if (empty($data)) {
            // Avoid a fatal error on trying to map data to null below.
            // Not 100% sure why/how this happens so will see what this change does... PW 12/2016
            return;
        }

        $formsOtherThanQuantity = [];
        foreach ($forms as $key => $form) {
            if ('quantity' === $form->getName()) {
                $targetQuantity = $form->getData();
                $this->orderItemQuantityModifier->modify($data, $targetQuantity);

                continue;
            }

            $formsOtherThanQuantity[] = $form;
        }

        if (!empty($formsOtherThanQuantity)) {
            $this->propertyPathDataMapper->mapFormsToData($formsOtherThanQuantity, $data);
        }
    }
}
