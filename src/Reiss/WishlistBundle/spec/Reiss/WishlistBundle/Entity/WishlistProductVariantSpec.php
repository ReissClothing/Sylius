<?php
/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date 06/08/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace spec\Reiss\WishlistBundle\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistItemSpec extends ObjectBehavior
{

    function it_does_not_belong_to_a_wishlit_by_default()
    {
        $this->getWishlist()->shouldReturn(null);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\WishlistItem');
    }

    function it_have_interface()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\WishlistItemInterface');
    }

    function its_position_is_mutable()
    {
        $this->setPosition(4);
        $this->getPosition()->shouldReturn(4);
    }

    function its_variant_is_mutable(ProductVariantInterface $variant)
    {
        $this->setVariant($variant);
        $this->getVariant()->shouldReturn($variant);
    }

    function its_wishlist_is_mutable(WishlistInterface $wishlist)
    {
        $this->setWishlist($wishlist);
        $this->getWishlist()->shouldReturn($wishlist);
    }

    function it_has_fluent_interface(WishlistInterface $wishlist, ProductVariantInterface $productVariant)
    {
        $this->setPosition(1)->shouldReturn($this);
        $this->setVariant($productVariant)->shouldReturn($this);
        $this->setWishlist($wishlist)->shouldReturn($this);
    }
}
