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

use Reiss\StoreBundle\Entity\Traits\TimestampableEntity;
use Reiss\StoreBundle\Entity\Traits\BlameableEntity;
use Reiss\StoreBundle\Entity\TimestampableInterface;
use Reiss\StoreBundle\Entity\BlameableInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistItem implements TimestampableInterface, BlameableInterface, WishlistItemInterface, ResourceInterface
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @var Integer
     */
    protected $id;

    /**
     * @var WishlistInterface
     */
    protected $wishlist;

    /**
     * @var \Reiss\ProductBundle\Entity\ProductVariant
     */
    protected $variant;

    /**
     * @var Integer
     */
    protected $position;

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
    public function setVariant($variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritDoc}
     */
    public function setWishlist($wishlist)
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getWishlist()
    {
        return $this->wishlist;
    }
}
