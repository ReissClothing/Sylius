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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Reiss\UserBundle\Entity\Customer;
use Reiss\WishlistBundle\Entity\WishlistItemInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\Wishlist');
    }

    function it_have_interface()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\WishlistInterface');
    }

    function its_name_is_mutable()
    {
        $this->setName('name');
        $this->getName()->shouldReturn('name');
    }

    function its_position_is_mutable()
    {
        $this->setPosition(3);
        $this->getPosition()->shouldReturn(3);
    }

    function its_primary_is_mutable()
    {
        $this->setPrimary(true);
        $this->getPrimary()->shouldReturn(true);
    }

    function its_customer_is_mutable(Customer $customer)
    {
        $this->setCustomer($customer);
        $this->getCustomer()->shouldReturn($customer);
    }

    function its_list_wishlist_item_is_mutable(ArrayCollection $wishlistItems)
    {
        $this->setWishlistItems($wishlistItems);
        $this->getWishlistItems()->shouldReturn($wishlistItems);
    }

    function it_has_no_product_variants_by_default()
    {
        $this->getWishlistItems()->count()->shouldReturn(0);
    }

    function it_adds_wishlist_item_correctly(WishlistItemInterface $wishlistItem)
    {
        $this->addWishlistItem($wishlistItem);

        $this->getWishlistItems()->count()->shouldReturn(1);
        $this->getWishlistItems()->first()->shouldReturn($wishlistItem);
    }

    function it_removes_product_variant_correctly(WishlistItemInterface $wishlistItem)
    {
        $this->addWishlistItem($wishlistItem);
        $this->getWishlistItems()->count()->shouldReturn(1);
        $this->getWishlistItems()->first()->shouldReturn($wishlistItem);

        $this->removeWishlistItem($wishlistItem);
        $this->getWishlistItems()->count()->shouldReturn(0);
    }

    function it_has_fluent_interface(
        Customer $customer,
        ArrayCollection $array,
        WishlistItemInterface $wishlistItem
    )
    {
        $this->setName('My name')->shouldReturn($this);
        $this->setPosition(1)->shouldReturn($this);
        $this->setPrimary(true)->shouldReturn($this);
        $this->setCustomer($customer)->shouldReturn($this);
        $this->setWishlistItems($array)->shouldReturn($this);
        $this->addWishlistItem($wishlistItem)->shouldReturn($this);
        $this->removeWishlistItem($wishlistItem)->shouldReturn($this);
    }
}