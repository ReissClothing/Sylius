<?php
 /**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      29/05/15
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Helper;

use Reiss\UserBundle\Entity\Customer;
use Symfony\Component\Routing\Router;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class FrontendHelper
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * @param Customer $customer
     *
     * @return string
     */
    public function wishlistGroupName(Customer $customer)
    {
        $name = $customer->getFirstName();
        if ("s" == substr($name, -1)) {
            return $name . "' " . "Wishlists";
        } else {
            return $name . "'s " . "Wishlists";
        }
    }

    /**
     * @param array $wishlists
     *
     * @return array
     */
    public function wishlistSharedArray($wishlists)
    {
        $wishlistShared = array();
        foreach ($wishlists as $wishlist) {
            if ($wishlist->getShareToken()) {
                $wishlistShared[$wishlist->getId()] =
                    $this->router->generate(
                        'reiss_wishlist_shared_wishlist', array('shareToken' => $wishlist->getShareToken()), true
                    );
            }
        }

        return $wishlistShared;
    }
}