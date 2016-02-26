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

use Reiss\StoreBundle\Manager\ReissManagerInterface;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Reiss\WishlistBundle\Entity\WishlistItemInterface;
use Reiss\ProductBundle\Entity\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;


/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
interface WishlistItemManagerInterface extends ReissManagerInterface
{
    /**
     * @param WishlistInterface       $wishlist
     * @param ProductVariantInterface $variant
     *
     * @return WishlistItemInterface $wishlistItem
     */
    public function createNewWishlistItem(WishlistInterface $wishlist, ProductVariantInterface $variant);

    /**
     * @param WishlistItemInterface $wishlistItem
     */
    public function updateWishlistItemDate(WishlistItemInterface $wishlistItem);
} 