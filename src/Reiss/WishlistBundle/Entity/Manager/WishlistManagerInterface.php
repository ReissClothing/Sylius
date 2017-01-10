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
use Reiss\UserBundle\Entity\Customer;
use Reiss\WishlistBundle\Entity\WishlistInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
interface WishlistManagerInterface extends ReissManagerInterface
{
    /**
     * @param Customer $customer
     * @param int           $position
     * @param bool          $primary
     * @param int           $wishlistNum
     *
     * @return WishlistInterface
     */
    public function createNewWishlist(Customer $customer, $position = 0, $primary = true, $wishlistNum = 0);

    /**
     * @param Customer $customer
     * @param int           $wishlistNum
     *
     * @return WishlistInterface
     */
    public function createNewWishlistLastPosition(Customer $customer, $wishlistNum);

    /**
     * @param WishlistInterface $wishlist
     */
    public function updateWishlistDate(WishlistInterface $wishlist);

    /**
     * @param WishlistInterface $wishlist
     *
     * @return String
     */
    public function createShareToken(WishlistInterface $wishlist);

    /**
     * @param WishlistInterface $wishlist
     */
    public function removeShareToken(WishlistInterface $wishlist);
}