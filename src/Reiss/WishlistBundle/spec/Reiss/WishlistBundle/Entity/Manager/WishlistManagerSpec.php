<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      06/08/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Reiss\WishlistBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Reiss\UserBundle\Entity\Customer;
use Reiss\WishlistBundle\Entity\Manager\WishlistItemManager;
use Reiss\WishlistBundle\Entity\Repository\WishlistRepository;
use Reiss\WishlistBundle\Entity\Wishlist;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistManagerSpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        WishlistItemManager $wishlistItemManager,
        WishlistRepository $wishlistRepository

    ) {
        $this->beConstructedWith(
            $em,
            'Reiss\WishlistBundle\Entity\Wishlist',
            $wishlistItemManager,
            $wishlistRepository,
            $translator
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\Manager\WishlistManager');
    }

    function it_have_interface()
    {
        $this->shouldHaveType('Reiss\WishlistBundle\Entity\Manager\WishlistManagerInterface');
    }

    function it_creates_a_new_wishlist_correctly(Customer $customer)
    {
        $wishlist = $this->createNewWishlist($customer);

        $wishlist->getPosition()->shouldReturn(0);
        $wishlist->getPrimary()->shouldReturn(true);
        $wishlist->getCustomer()->shouldReturn($customer);
    }

    function it_has_to_update_the_date_correctly(Customer $customer)
    {
        $wishlist = $this->createNewWishlist($customer);

        $this->updateWishlistDate($wishlist);
        $wishlist->getUpdatedAt()->shouldBeLike(new \DateTime ('now'));
    }

    function it_has_to_create_share_token(Customer $customer)
    {
        $wishlist = $this->createNewWishlist($customer);

        $this->createShareToken($wishlist);
        $wishlist->getShareToken()->shouldNotBe(null);
    }

    function it_has_to_remove_share_token_correctly(Customer $customer)
    {
        $wishlist = $this->createNewWishlist($customer);

        $this->createShareToken($wishlist);
        $this->removeShareToken($wishlist);
        $wishlist->getShareToken()->shouldBe(null);
    }

    function it_ensures_a_primary_wishlist_when_there_arent_any(Customer $customer)
    {
        $wishlist1 = new Wishlist();
        $wishlist2 = new Wishlist();
        $wishlist3 = new Wishlist();

        $arrayCollection = new ArrayCollection([$wishlist1, $wishlist2, $wishlist3]);

        $customer->getWishlists()->willReturn($arrayCollection);

        $wishlist = $this->ensurePrimaryWishlistIfExists($customer);

        $wishlist->getPrimary()->shouldBe(true);
        $wishlist->shouldEqual($wishlist1);
    }

    function it_doesnt_set_primary_when_customer_has_no_wishlists(Customer $customer)
    {
        $customer->getWishlists()->willReturn(new ArrayCollection());

        $this->ensurePrimaryWishlistIfExists($customer)->shouldReturn(null);
    }

    function it_doesnt_set_primary_when_customer_already_has_primary(Customer $customer)
    {
        $wishlist1 = new Wishlist();
        $wishlist2 = new Wishlist();
        $wishlist2->setPrimary(true);
        $wishlist3 = new Wishlist();

        $arrayCollection = new ArrayCollection([$wishlist1, $wishlist2, $wishlist3]);

        $customer->getWishlists()->willReturn($arrayCollection);

        $this->ensurePrimaryWishlistIfExists($customer)->shouldReturn(null);
    }
} 