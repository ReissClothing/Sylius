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

use Doctrine\ORM\EntityManagerInterface;

/**
 * Base manager class for all Reiss entities
 *
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
class ReissManager implements ReissManagerInterface
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * Entity class name
     *
     * @var string
     */
    protected $className;

    /**
     * Entity repository
     *
     * @var Doctrine\ORM\EntityRepository
     */
    protected $er;

    /**
     * @param EntityManagerInterface $em
     * @param string                 $className
     */
    public function __construct(
        EntityManagerInterface $em,
        $className
    ) {
        $this->em        = $em;
        $this->className = $className;
        $this->er        = $em->getRepository($this->className);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $className = $this->className;

        return new $className;
    }

    /**
     * {@inheritdoc}
     */
    public function persist($entity)
    {
        $this->checkClass($entity);

        $this->em->persist($entity);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->em->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->em->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function persistAndFlush($entity)
    {
        $this->persist($entity);
        $this->em->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity)
    {
        $this->checkClass($entity);

        $this->em->remove($entity);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAndFlush($entity)
    {
        $this->remove($entity);
        $this->em->flush();

        return $this;
    }

    /**
     * Checks if a given entity can be managed by the manager
     *
     * @param $entity
     *
     * @throws \InvalidArgumentException
     */
    private function checkClass($entity)
    {
        if (!$entity instanceof $this->className) {
            throw new \InvalidArgumentException(
                sprintf('Entity should be instance of %s',
                    $this->className)
            );
        }
    }
} 