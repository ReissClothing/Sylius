<?php
/**
 * @author    Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 * @date      16/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\StoreBundle\Manager;

/**
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
interface ReissManagerInterface
{
    /**
     * Create a new resource
     *
     * @return mixed
     */
    public function createNew();

    /**
     * Tells the EntityManager to make an instance managed and persistent.
     *
     * @param $entity
     *
     * @return ReissManagerInterface
     */
    public function persist($entity);

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     *
     * @return ReissManagerInterface
     */
    public function flush();

    /**
     * Calls clear on the underlying EntityManager
     */
    public function clear();

    /**
     * Tells the EntityManager to make an instance managed and persistent.
     * Flushes all changes to objects that have been queued up to now to the database
     *
     * @param $entity
     *
     * @return ReissManagerInterface
     */
    public function persistAndFlush($entity);

    /**
     * Removes an entity instance.
     *
     * @param $entity
     *
     * @return ReissManagerInterface
     */
    public function remove($entity);

    /**
     * Removes an entity instance.
     * Flushes all changes to objects that have been queued up to now to the database
     *
     * @param $entity
     *
     * @return ReissManagerInterface
     */
    public function removeAndFlush($entity);
} 