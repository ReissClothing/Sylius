<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      30/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Entity\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Reiss\OrderBundle\Entity\Order;
use Reiss\ProductBundle\Entity\ProductVariant;
use Reiss\StoreBundle\Manager\ReissManager;
use Reiss\UserBundle\Entity\Customer;
use Reiss\WishlistBundle\Entity\Repository\WishlistRepository;
use Reiss\WishlistBundle\Entity\Wishlist;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistManager extends ReissManager implements WishlistManagerInterface
{
    /**
     * @var WishlistItemManager
     */
    protected $wishlistItemManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * @param EntityManagerInterface $em
     * @param string                 $className
     * @param TranslatorInterface    $translator
     *
     */
    public function __construct(
        EntityManagerInterface $em,
        $className,
        WishlistItemManager $wishlistItemManager,
        WishlistRepository $wishlistRepository,
        TranslatorInterface $translator
    ) {
        $this->wishlistItemManager = $wishlistItemManager;
        $this->wishlistRepository  = $wishlistRepository;
        $this->translator          = $translator;

        parent::__construct($em, $className);
    }

    /**
     * {@inheritDoc}
     */
    public function createNewWishlist(Customer $customer, $position = 0, $primary = true, $wishlistNum = 0)
    {
        $wishlist = $this->createNew();

        $wishlistNum++;

        $wishlist
            ->setCustomer($customer)
            ->setPosition($position)
            ->setName($this->translator->trans("reiss.frontend.wishlist.title.my_collection", array('%wishlistNum%' => $wishlistNum)))
            ->setPrimary($primary);

        $this->persistAndFlush($wishlist);

        return $wishlist;
    }

    /**
     * {@inheritDoc}
     */
    public function createNewWishlistLastPosition(Customer $customer, $wishlistNum)
    {
        if ($wishlistNum == 0) {
            return $this->createNewWishlist($customer, -1, true, $wishlistNum);
        } else {
            return $this->createNewWishlist($customer, -1, false, $wishlistNum);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateWishlistDate(WishlistInterface $wishlist)
    {
        $wishlist->setUpdatedAt(new \DateTime());

        $this->persistAndFlush($wishlist);
    }

    /**
     * {@inheritDoc}
     */
    public function createShareToken(WishlistInterface $wishlist)
    {
        if (null === $wishlist->getShareToken()) {
            $wishlist->setShareToken(sha1(uniqid($wishlist->getId(), true)));
            $this->flush();
        }

        return $wishlist->getShareToken();
    }

    /**
     * {@inheritDoc}
     */
    public function removeShareToken(WishlistInterface $wishlist)
    {
        $wishlist->setShareToken(null);

        $this->flush();
    }

    /**
     * @param Customer       $customer
     * @param ProductVariant $variant
     *
     * @return bool
     */
    public function addItemToPrimaryWishlist(Customer $customer, ProductVariant $variant)
    {
        if (!($wishlist = $this->er->getPrimaryWishlist($customer))) {
            $wishlist = $this->createNewWishlist($customer);
        }

        $success = $this->wishlistItemManager->ensureVariant($wishlist, $variant);

        $this->updateWishlistDate($wishlist);

        $this->flush();

        return $success;
    }

    /**
     * @param Customer $customer
     *
     * @return Wishlist|null
     */
    public function ensurePrimaryWishlistIfExists(Customer $customer)
    {
        $wishlists = $customer->getWishlists();

        if ($wishlists && $wishlists->count()) {
            $hasPrimary = false;

            foreach ($wishlists as $wishlist) {
                if (true === $wishlist->getPrimary()) {
                    $hasPrimary = true;
                }
            }

            if (!$hasPrimary) {
                $firstWishlist = $wishlists->first();
                $firstWishlist->setPrimary(true);
                $this->flush();

                return $firstWishlist;
            }
        }
    }

    /**
     * @param Wishlist $wishlist
     * @param Order    $order
     *
     * @return array
     */
    public function insertWishlistItems(Wishlist $wishlist, Order $order)
    {
        $toTranslate      = 'reiss.frontend.wishlist.alerts.wishlist_item_added_from_cart';
        $numVariantsAdded = 0;

        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();

            $alreadyInWishlist = false;
            foreach ($wishlist->getWishlistItems() as $wishlistItem) {
                if ($wishlistItem->getVariant()->getId() == $variant->getId()) {
                    $alreadyInWishlist = true;
                }
            }

            if (!$alreadyInWishlist) {
                $this->wishlistItemManager->createNewWishlistItem($wishlist, $variant);
                $numVariantsAdded++;
            }
        }

        if ($numVariantsAdded > 1) {
            $toTranslate = "reiss.frontend.wishlist.alerts.wishlist_items_added_from_cart";
        }

        return array('toTranslate' => $toTranslate, 'numVariantsAdded' => $numVariantsAdded);
    }
}