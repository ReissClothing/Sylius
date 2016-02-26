<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      22/05/15
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Helper;

use Reiss\AddressingBundle\Entity\Zone;
use Reiss\ProductBundle\Entity\Season;
use Reiss\WishlistBundle\Entity\Repository\WishlistRepository;
use Reiss\WishlistBundle\Entity\Repository\WishlistItemRepository;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class StatisticsHelper
{
    /**
     * @var WishlistRepository
     */
    protected $wRepository;

    /**
     * @var WishlistItemRepository
     */
    protected $wvRepository;

    /**
     * @param WishlistRepository     $wRepository
     * @param WishlistItemRepository $wvRepository
     */
    public function __construct
    (
        WishlistRepository $wRepository,
        WishlistItemRepository $wvRepository
    )
    {
        $this->wRepository  = $wRepository;
        $this->wvRepository = $wvRepository;
    }

    /**
     * TODO: Waiting to decide the way to build wishlists statistics...
     *
     * @param null   $gender
     * @param Zone   $zone
     * @param Season $season
     */
    public function getMaleStatistics($gender = null, Zone $zone = null, Season $season = null)
    {
//        $wishlistsStatistics = $this->wRepository->getWishlistNum($gender);
//        $productsStatistics = $this->wvRepository->getProducts($gender, $zone, $season);
//        $this->wRepository->getWishlistCount();
    }
}