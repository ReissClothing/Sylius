<?php
/**
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 * @date 28/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\StoreBundle\Entity;

use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
interface BlameableInterface
{
    /**
     * Get creation time.
     *
     * @return UserInterface
     */
    public function getCreatedBy();

    /**
     * Get the time of last update.
     *
     * @return UserInterface
     */
    public function getUpdatedBy();

    /**
     * Set creation time.
     *
     * @param UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy);

    /**
     * Set the time of last update.
     *
     * @param UserInterface $updatedBy
     */
    public function setUpdatedBy(UserInterface $updatedBy);
} 