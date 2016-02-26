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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Reiss\WishlistBundle\Entity\WishlistInterface;
use Doctrine\ORM\Query;
use Sylius\Component\User\Model\Customer;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistRepository extends EntityRepository
{
    /**
     * @param Customer $customer
     *
     * @return WishlistInterface $wishlist
     */
    public function getPrimaryWishlist($customer)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('Wishlist');
        $qb
            ->select('Wishlist')
            ->from('Reiss\WishlistBundle\Entity\Wishlist', 'Wishlist')
            ->where($qb->expr()->eq('Wishlist.primary', ':primary'))
            ->andWhere($qb->expr()->eq('Wishlist.customer', ':customer'))
            ->setParameter('primary', true)
            ->setParameter('customer', $customer);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param Customer $customer
     *
     * @return array
     */
    public function getWishlistsByCustomer($customer)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('Wishlist');
        $qb
            ->select('Wishlist')
            ->from('Reiss\WishlistBundle\Entity\Wishlist', 'Wishlist')
            ->where("Wishlist.customer = :customer")
            ->setParameter('customer', $customer)
            ->addOrderBy('Wishlist.position', 'ASC')
            ->addOrderBy('Wishlist.id', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
    }

    /**
     * @return array
     */
    public function getWishlistCount()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('count(wishlist.id)')
            ->from('Reiss\WishlistBundle\Entity\Wishlist', 'wishlist')
        ;

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @return array
     */
    public function getWishlistCountsByGender()
    {
        $queryResult = $this
            ->getEntityManager()
            ->createQuery('
                SELECT COUNT(w) times, u.gender
                FROM Reiss\WishlistBundle\Entity\Wishlist w
                    JOIN w.customer u
                GROUP BY u.gender')
            ->getResult(Query::HYDRATE_ARRAY);

        return $queryResult;
    }

    /**
     * @return array
     */
    public function getCustomerWithWishlistsCountsByGender()
    {
        $queryResult = $this
            ->getEntityManager()
            ->createQuery('
                SELECT COUNT(DISTINCT u.id) times, u.gender
                FROM Reiss\WishlistBundle\Entity\Wishlist w
                    JOIN w.customer u
                GROUP BY u.gender')
            ->getResult(Query::HYDRATE_ARRAY);

        $responseArray = array();

        $responseArray[Customer::MALE_GENDER]   = 0;
        $responseArray[Customer::FEMALE_GENDER] = 0;

        foreach ($queryResult as $query) {
            $responseArray[$query['gender']] = $query['times'];
        }

        return $responseArray;
    }

    /**
     * We don't need DISTINCT in the query because if you ask for the whole object is not going to
     * repeat the element, so if you want to get the elements repeated you just need to make the query
     * with the fields specified.
     *
     * @return array
     */
    public function getProductsByWishlist()
    {
        $qb = $this->getEntityManager()->createQueryBuilder('Product');

        $qb
            ->select('Product')
            ->from('Reiss\ProductBundle\Entity\Product', 'Product')
            ->join('Product.variants', 'Variant')
            ->join('Reiss\WishlistBundle\Entity\WishlistItem', 'WishlistItem',
                'WITH', 'WishlistItem.variant = Variant')
            ->join('Reiss\WishlistBundle\Entity\Wishlist', 'Wishlist',
                'WITH', 'Wishlist = WishlistItem.wishlist');

        $query = $qb->getQuery();

        return $query->getResult();
    }
}