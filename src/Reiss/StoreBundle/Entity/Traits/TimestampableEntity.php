<?php
/**
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 * @date 28/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\StoreBundle\Entity\Traits;


/**
 * Timestampable Trait, usable with PHP >= 5.4
 *
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
trait TimestampableEntity
{

    /**
     * Creation time.
     *
     * @var
     */
    protected $createdAt;


    /**
     * Last update time.
     *
     * @var
     */
    protected $updatedAt;


    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
