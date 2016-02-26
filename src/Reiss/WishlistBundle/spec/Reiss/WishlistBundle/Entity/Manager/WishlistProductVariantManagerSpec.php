<?php
/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date 06/08/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Reiss\WishlistBundle\Entity\Manager;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Reiss\ProductBundle\Entity\ProductVariant;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistItemManagerSpec extends ObjectBehavior
{

    protected $validationGroups = array('sylius');

    function let(EntityManagerInterface $em)
    {
        $this->beConstructedWith(
            $em,
            'Reiss\WishlistBundle\Entity\WishlistItem',
            $this->validationGroups
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\Manager\WishlistItemManager');
    }

    function it_have_interface()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\Manager\WishlistItemManagerInterface');
    }

    function it_have_to_create_a_new_product_variant(WishlistInterface $wishlist, ProductVariant $variant)
    {
        $productVariant = $this->createNewProductVariant($wishlist, $variant);
        $productVariant->getVariant()->shouldReturn($variant);
    }
}