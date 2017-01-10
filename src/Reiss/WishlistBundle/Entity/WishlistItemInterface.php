<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      30/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Entity;

use Reiss\ProductBundle\Entity\ProductVariant;


/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
interface WishlistItemInterface
{
    /**
     * @return Integer
     */
    public function getId();

    /**
     * @param Integer $position
     *
     * @return WishlistItemInterface
     */
    public function setPosition($position);

    /**
     * @return Integer
     */
    public function getPosition();

    /**
     * @param ProductVariant $variant
     *
     * @return WishlistItemInterface
     */
    public function setVariant($variant);

    /**
     * @return ProductVariant
     */
    public function getVariant();

    /**
     * @param WishlistInterface $wishlist
     *
     * @return WishlistItemInterface
     */
    public function setWishlist($wishlist);

    /**
     * @return WishlistInterface
     */
    public function getWishlist();
}