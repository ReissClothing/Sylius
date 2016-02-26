<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      11/09/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Reiss\AddressingBundle\Entity\Zone;
use Doctrine\ORM\EntityRepository;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class ZoneType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zone', 'entity', array(
                'class'         => 'ReissAddressingBundle:Zone',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('z')
                            ->where('z.abbreviation = price');
                    },
                'property'      => 'name',
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reiss_wishlist_zone';
    }
} 