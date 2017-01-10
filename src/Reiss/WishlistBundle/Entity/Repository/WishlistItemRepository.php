<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      30/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Entity\Repository;

use Reiss\ProductBundle\Entity\ProductVariant;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Reiss\WishlistBundle\Entity\WishlistItemInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistItemRepository extends EntityRepository
{
    /**
     * @param WishlistInterface $wishlist
     * @param ProductVariant    $variant
     *
     * @return WishlistItemInterface $wishlistItem
     */
    public function getWishlistItemByVariant($wishlist, $variant)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('WishlistItem');
        $qb
            ->select('WishlistItem')
            ->from('Reiss\WishlistBundle\Entity\WishlistItem', 'WishlistItem')
            ->where($qb->expr()->eq('WishlistItem.variant', ':variant'))
            ->andWhere($qb->expr()->eq('WishlistItem.wishlist', ':wishlist'))
            ->setParameter('variant', $variant)
            ->setParameter('wishlist', $wishlist);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Waiting for Product type implementation
     *
     * @param String $gender
     * @param Int    $season
     *
     * @return array
     */
    public function filterTopProducts($gender, $season)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('Product');

        $qb
            ->select('Product.id, count(Product.id), count(Product.id) as HIDDEN c')
            ->from('Reiss\ProductBundle\Entity\Product', 'Product')
            ->join('Product.variants', 'Variant')
            ->join('Reiss\WishlistBundle\Entity\WishlistItem', 'WishlistItem',
                'WITH', 'WishlistItem.variant = Variant')
            ->join('Reiss\WishlistBundle\Entity\Wishlist', 'Wishlist',
                'WITH', 'Wishlist = WishlistItem.wishlist')
            ->join('Reiss\UserBundle\Entity\Customer', 'Customer',
                'WITH', 'Wishlist.customer = Customer');
        $qb
            ->where('Product.season = :season');

        if (!is_null($gender)) {
            $qb->andWhere("Customer.gender = :gender");
        }
        $qb
            ->groupBy('Product.id')
            ->orderBy('c', 'DESC');

        if (!is_null($gender)) {
            $qb->setParameter('gender', $gender);
        }

        $qb->setParameter('season', $season);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return array
     */
    public function getProductCountByGenderAndMarkdown()
    {
        $queryResult = $this
            ->getEntityManager()
            ->createQuery('
                SELECT COUNT(DISTINCT product.id) times, c.gender, productPrice.markdown
                FROM Reiss\ProductBundle\Entity\Product product
                    JOIN product.variants variant
                    JOIN Reiss\WishlistBundle\Entity\WishlistItem wishlistItem WITH wishlistItem.variant = variant
                    JOIN Reiss\WishlistBundle\Entity\Wishlist wishlist WITH wishlistItem.wishlist = wishlist
                    JOIN Reiss\UserBundle\Entity\Customer c WITH c = wishlist.customer
                    JOIN Reiss\ProductBundle\Entity\ProductPrice productPrice WITH productPrice.product = product
                    JOIN Reiss\CoreBundle\Entity\Channel channel WITH productPrice.channel = channel
                WHERE channel.name = :channel

                GROUP BY c.gender, productPrice.markdown')
            // TODO Fernando inject zone
            ->setParameter('channel', "UK")
            ->getResult(Query::HYDRATE_ARRAY);

        return $this->buildSaleArray($queryResult);
    }

    /**
     * @param Customer $customer
     *
     * @return array
     */
    public function getProductsByCustomer($customer, $maxResult = 8)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT wishlistItem, v, p
            FROM  Reiss\WishlistBundle\Entity\WishlistItem wishlistItem
                JOIN wishlistItem.wishlist w
                JOIN wishlistItem.variant v
                JOIN v.object p
            WHERE w.customer = :customer
            ORDER BY wishlistItem.createdAt DESC
        ');

        $query
            ->setParameter('customer', $customer)
            ->setMaxResults($maxResult);

        return $query->getResult();
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return int
     */
    public function getQuantityForCustomer(CustomerInterface $customer)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(i)
                FROM Reiss\WishlistBundle\Entity\WishlistItem i
                    JOIN i.wishlist w
                WHERE w.customer = :customer
            ')
            ->setParameter('customer', $customer)
            ->getSingleScalarResult();
    }

    /**
     * @param array $queryArray
     *
     * @return array
     */
    private function buildSaleArray($queryArray)
    {
        $responseArray = array();

        // Initialize array with empty values
        foreach ($queryArray as $query) {
            $responseArray[$query['gender']][true]  = 0;
            $responseArray[$query['gender']][false] = 0;
        }

        foreach ($queryArray as $query) {
            $responseArray[$query['gender']][$query['markdown']] = $query['times'];
        }

        return $responseArray;
    }

    /**
     * @param int $offset
     * @param int $chunk
     * @param string $dateSince
     *
     * @return array
     */
    public function getForResponsysExport($offset, $chunk, $dateSince)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('
                c.number,
                pv.sku,
                DATE(wi.createdAt)
            ')
            ->from('Reiss\WishlistBundle\Entity\WishlistItem', 'wi')
            ->leftJoin('wi.wishlist', 'w')
            ->leftJoin('wi.variant', 'pv')
            ->leftJoin('w.customer', 'c')
            ->setMaxResults($chunk)
            ->setFirstResult($offset);

        if ($dateSince) {
            $q->where('wi.updatedAt >= :dateSince')
                ->setParameter('dateSince', $dateSince);
        }

        return $q->getQuery()
            ->getArrayResult();
    }
}