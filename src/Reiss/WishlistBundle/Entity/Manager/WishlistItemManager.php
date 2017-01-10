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

use Reiss\StoreBundle\Manager\ReissManager;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Reiss\WishlistBundle\Entity\WishlistItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistItemManager extends ReissManager implements WishlistItemManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function createNewWishlistItem(WishlistInterface $wishlist, ProductVariantInterface $variant)
    {
        $productVariant = $this->createNew();

        $productVariant
            ->setWishlist($wishlist)
            ->setVariant($variant);

        $this->persistAndFlush($productVariant);

        return $productVariant;
    }

    /**
     * {@inheritDoc}
     */
    public function updateWishlistItemDate(WishlistItemInterface $wishlistItem)
    {
        $wishlistItem->setUpdatedAt(new \DateTime());

        $this->persistAndFlush($wishlistItem);
    }

    /**
     * Ensure we have the following product variant on this wishlist
     * Return true if added, false if already existed and we just updated it's datetime
     *
     * @param WishlistInterface       $wishlist
     * @param ProductVariantInterface $variant
     *
     * @return bool
     */
    public function ensureVariant(WishlistInterface $wishlist, ProductVariantInterface $variant)
    {
        if (!$wishlistItem = $this->er->getWishlistItemByVariant($wishlist, $variant)) {
            $this->createNewWishlistItem($wishlist, $variant);

            return true;
        }

        $this->updateWishlistItemDate($wishlistItem);

        return false;
    }
}