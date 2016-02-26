<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      01/09/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class StatisticsFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', 'entity', array(
                'class'    => 'ReissProductBundle:Season',
                'property' => 'code',
            ))
            ->add('gender', 'reiss_gender_people_choice', array(
                'empty_value' => 'All',
                'required'    => false
            ));
            // TODO @Gonzalo use channel
//            ->add('zone', 'reiss_zone_choice_content', array());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reiss_wishlist_filter';
    }
} 