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

use Reiss\UserBundle\Entity\Customer;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
interface WishlistInterface
{
    /**
     * @return \Datetime
     */
    public function getCreatedAt();

    /**
     * @return \Datetime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return WishlistInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @param \DateTime $updatedAt
     *
     * @return WishlistInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * @return Integer
     */
    public function getId();

    /**
     * @param String $name
     *
     * @return WishlistInterface
     */
    public function setName($name);

    /**
     * @return String
     */
    public function getName();

    /**
     * @param Integer $position
     *
     * @return WishlistInterface
     */
    public function setPosition($position);

    /**
     * @return Integer
     */
    public function getPosition();

    /**
     * @param boolean $primary
     *
     * @return WishlistInterface
     */
    public function setPrimary($primary);

    /**
     * @return boolean
     */
    public function getPrimary();

    /**
     * @return String
     */
    public function getShareToken();

    /**
     * @param String $shareToken
     *
     * @return WishlistInterface
     */
    public function setShareToken($shareToken);

    /**
     * @param Customer $customer
     *
     * @return WishlistInterface
     */
    public function setCustomer($customer);

    /**
     * @return Customer
     */
    public function getCustomer();

    /**
     * @return WishlistItemInterface[]
     */
    public function getWishlistItems();

    /**
     * @param WishlistItemInterface[] $wishlistItems
     *
     * @return WishlistInterface
     */
    public function setWishlistItems($wishlistItems);

    /**
     * @param WishlistItemInterface $wishlistItem
     *
     * @return WishlistInterface
     */
    public function addWishlistItem(WishlistItemInterface $wishlistItem);

    /**
     * @param WishlistItemInterface $wishlistItem
     *
     * @return WishlistInterface
     */
    public function removeWishlistItem(WishlistItemInterface $wishlistItem);
}