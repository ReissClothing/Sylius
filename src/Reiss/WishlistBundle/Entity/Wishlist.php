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

use Doctrine\Common\Collections\ArrayCollection;
use Reiss\StoreBundle\Entity\Traits\TimestampableEntity;
use Reiss\StoreBundle\Entity\Traits\BlameableEntity;
use Reiss\StoreBundle\Entity\TimestampableInterface;
use Reiss\StoreBundle\Entity\BlameableInterface;
use Reiss\UserBundle\Entity\Customer;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class Wishlist implements TimestampableInterface, BlameableInterface, WishlistInterface, ResourceInterface
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @var Integer
     */
    protected $id;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var String
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $primary;

    /**
     * @var Integer
     */
    protected $position;

    /**
     * @var String
     */
    protected $shareToken;

    /**
     * @var ArrayCollection
     */
    protected $wishlistItems;

    public function __construct()
    {
        $this->wishlistItems = new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setShareToken($shareToken)
    {
        $this->shareToken = $shareToken;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getShareToken()
    {
        return $this->shareToken;
    }

    /**
     * {@inheritDoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function setPrimary($primary)
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * {@inheritDoc}
     */
    public function getWishlistItems()
    {
        return $this->wishlistItems;
    }

    /**
     * {@inheritDoc}
     */
    public function setWishlistItems($wishlistItems)
    {
        $this->wishlistItems = $wishlistItems;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addWishlistItem(WishlistItemInterface $wishlistItem)
    {
        $this->wishlistItems->add($wishlistItem);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeWishlistItem(WishlistItemInterface $wishlistItem)
    {
        $this->wishlistItems->removeElement($wishlistItem);

        return $this;
    }

    /**
     * @return String
     */
    public function __toString()
    {
        return $this->name;
    }
}