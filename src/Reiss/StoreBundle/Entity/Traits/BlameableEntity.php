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

use Sylius\Component\Core\Model\UserInterface;

/**
 * Blameable Trait, usable with PHP >= 5.4
 *
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
trait BlameableEntity
{
    /**
     * User that created entity
     *
     * @var
     */
    private $createdBy;

    /**
     * User that  last updated entity
     *
     * @var
     */
    private $updatedBy;

    /**
     * {@inheritdoc}
     */
    public function setCreatedBy(UserInterface $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedBy(UserInterface $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}