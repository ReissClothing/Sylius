<?php
/**
 * @author    Craig R Morton <craig.morton@reiss.com>
 * @date      06/01/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Reiss\UserBundle\Entity\Customer;
use Reiss\WishlistBundle\Entity\Wishlist;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WishlistChoiceType extends AbstractType
{
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var WishlistInterface
     */
    protected $wishlist;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param Customer            $customer
     * @param WishlistInterface   $wishlist
     * @param TranslatorInterface $translator
     */
    public function __construct(Customer $customer, WishlistInterface $wishlist, TranslatorInterface $translator)
    {
        $this->customer   = $customer;
        $this->wishlist   = $wishlist;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wishlist', 'entity', array(
                'class'         => 'ReissWishlistBundle:Wishlist',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->where('w.customer = :customer')
                        ->andWhere('w.id != :wishlistId')
                        ->setParameter('customer', $this->customer)
                        ->setParameter('wishlistId', $this->wishlist->getId());
                },
                'property'      => 'name',
                'attr'          => array('class' => 'js-move-to-wishlist'),
                'empty_value'   => $this->translator->trans('reiss.frontend.wishlist.overlay.move_to_list'),
                'required'      => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Reiss\WishlistBundle\Entity\Wishlist'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'reiss_wishlist_choice';
    }
}