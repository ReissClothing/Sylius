<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      01/12/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Configure;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\OptionListAttributeConfigurationType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class OptionListAttributeFormOptionConfigurer
{
    /**
     * @param AttributeInterface           $attribute
     * @param AttributeValueInterface|null $attributeValue
     *
     * @return array
     */
    public function getOptionsToOptionListType(AttributeInterface $attribute, AttributeValueInterface $attributeValue = null)
    {
        $configuration = $attribute->getConfiguration();

        if ($attributeValue) {
            $attributeValue->ensureValidOptionListValue($configuration['format']);
        }

        $options['empty_value'] = '-- None --';
        $options['expanded']    = isset($configuration['expanded']) ? $configuration['expanded'] : false;
        $options['multiple']    = OptionListAttributeConfigurationType::MULTI_VALUE === $configuration['format'];

        $choices = [];
        foreach ($configuration['values'] as $value) {
            $choices[$value['value']] = $value['value'];
        }

        $options['choices'] = $choices;

        return $options;
    }
}
