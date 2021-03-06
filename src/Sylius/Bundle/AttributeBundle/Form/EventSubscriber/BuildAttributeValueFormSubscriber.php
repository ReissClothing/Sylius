<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\EventSubscriber;

use Sylius\Component\Attribute\AttributeType\OptionListAttributeType;
use Sylius\Component\Attribute\Configure\OptionListAttributeFormOptionConfigurer;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValue;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeValueFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var OptionListAttributeFormOptionConfigurer
     */
    protected $optionListAttributeFormOptionConfigurer;

    /**
     * @param RepositoryInterface $attributeRepository
     * @param OptionListAttributeFormOptionConfigurer $optionListAttributeFormOptionConfigurer
     */
    public function __construct(RepositoryInterface $attributeRepository, OptionListAttributeFormOptionConfigurer $optionListAttributeFormOptionConfigurer)
    {
        $this->attributeRepository = $attributeRepository;
        $this->optionListAttributeFormOptionConfigurer = $optionListAttributeFormOptionConfigurer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $attributeValue = $event->getData();

        if (null === $attributeValue || null === $attributeValue->getAttribute()) {
            return;
        }

        $this->addValueField($event->getForm(), $attributeValue->getAttribute(), $attributeValue);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $attributeValue = $event->getData();

        if (!isset($attributeValue['attribute'])) {
            throw new \InvalidArgumentException('Cannot create an attribute value form on pre submit event without an "attribute" key in data.');
        }

        $form = $event->getForm();
        $attribute = $this->attributeRepository->find($attributeValue['attribute']);

        $this->addValueField($form, $attribute);
    }

    /**
     * @param FormInterface      $form
     * @param AttributeInterface $attribute
     * @param AttributeValueInterface|null $attributeValue
     */
    private function addValueField(
        FormInterface $form,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue = null
    ) {
        $options = ['auto_initialize' => false, 'label' => $attribute->getName()];

        if (OptionListAttributeType::TYPE === $attribute->getType()) {
            $options = array_merge($options, $this->optionListAttributeFormOptionConfigurer->getOptionsToOptionListType($attribute, $attributeValue));
        }

        $form->add('value', 'sylius_attribute_type_'.$attribute->getType(), $options);
    }
}
