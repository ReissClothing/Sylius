<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      07/11/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class OptionListAttributeConfigurationType extends AbstractType
{
    const FORMAT_DROPDOWN           = 'dropdown';
    const FORMAT_MULTIPLE_SELECTION = 'multiple_selection';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('format', 'choice', [
                'choices' => [
                    self::FORMAT_DROPDOWN           => 'Dropdown',
                    self::FORMAT_MULTIPLE_SELECTION => 'Multiple selection',
                ],
                'attr' => [
                    'class' => 'js-format-selector'
                ],
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $attr = $form->getParent()->getNormData();

                $form
                    // We'll have minimum two fields for values
                    ->add('values', 'collection', [
                        'options'      => ['label' => false],
                        'label'        => false,
                        'entry_type'   => OptionListValueType::class,
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'data'         => isset($attr->getConfiguration()['values']) ? $attr->getConfiguration()['values'] : [[], []],
                    ])
                    ->add('addValue', 'button', [
                        'attr' => [
                            'class' => 'add-value btn btn-primary pull-right'
                        ]
                    ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
                // Removing empty inputs if they don't have any data
                $data  = $event->getData();
                $error = false;

                if (isset($data['values'])) {
                    foreach ($data['values'] as $key => $input) {
                        if (!$input['value']) {
                            unset($data['values'][$key]);
                        }
                    }

                    if (2 > count($data['values'])) {
                        $error = true;
                    }
                } else {
                    $error = true;
                }

                if ($error) {
                    $error = new FormError('You should at least set two values for this attribute.');
                    $event->getForm()->get('values')->addError($error);
                } else {
                    $event->setData($data);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_attribute_type_configuration_option_list';
    }
}
