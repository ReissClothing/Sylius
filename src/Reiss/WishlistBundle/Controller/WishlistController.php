<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      31/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Component\Core\Model\Customer;
use Reiss\ProductBundle\Entity\Product;
use Reiss\ProductBundle\Entity\ProductVariant;
use Reiss\StoreBundle\Controller\ReissBaseController;
use Reiss\StoreBundle\Response\AjaxJsonResponse;
use Reiss\WishlistBundle\Entity\Wishlist;
use Reiss\WishlistBundle\Exception\WishlistItemUnavailableException;
use Reiss\WishlistBundle\Form\Type\WishlistChoiceType;
use Reiss\WishlistBundle\Form\Type\StatisticsFilterType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistController extends ReissBaseController
{
    /**
     * @return Response
     */
    public function showWishlistsAction()
    {
        $wishlists      = $this->get('reiss.repository.wishlist')->getWishlistsByCustomer($this->getCustomer());
        $wishlistHelper = $this->get('reiss.wishlist_frontend.helper');

        return $this->render('ReissWishlistBundle:Frontend/Wishlist:index.html.twig',
            array(
                'wishlists'      => $wishlists,
                'wishlistsName'  => $wishlistHelper->wishlistGroupName($this->getCustomer()),
                'wishlistShared' => $wishlistHelper->wishlistSharedArray($wishlists)
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addProductToWishlistAfterLoginAction(Request $request, $variantId)
    {
        // The main firewall requires the user to be logged in, so we have a logged in customer
        $itemsPending = $request->getSession()->get('items.pending.wishlist');

        // Get the variant referer uri, as if the user has several tabs we might have a few variants that are pending
        $uri = $itemsPending[$variantId];

        try {
            foreach ($itemsPending as $id => $itemPending) {
                if ($variant = $this->addItemToPrimaryWishlist($id)) {
                    $this->addTranslatedFlash('success', 'reiss.frontend.wishlist.messages.success');
                } else {
                    $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.messages.already_database');
                }
            }

            $request->getSession()->remove('items.pending.wishlist');

            return $this->redirect($uri);

        } catch (\Exception $e) {
            $this->addTranslatedFlash('error', 'Error adding item to your wishlist: ', array('%error%' => $e->getMessage()));

            return $this->redirect($uri);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addProductToWishlistAction(Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            if ($request->isXmlHttpRequest()) {
                // TODO: Not sure if this one should stay a 400 or use AjaxJsonResponse @gonzalo?
                return new JsonResponse(null, 400);
            }

            // User not logged in, so we store the variant id to be added later when the user logs in
            $formValues = $request->request->get('sylius_cart_item');

            $itemsPending = array();
            if ($request->getSession()->has('items.pending.wishlist')) {
                $itemsPending = $request->getSession()->get('items.pending.wishlist');
            }

            $itemsPending[$formValues['variant']] = $request->headers->get('referer');

            $request->getSession()->set('items.pending.wishlist', $itemsPending);

            return $this->redirect($this->generateUrl('reiss_wishlist_add_product_after_login', array('variantId' => $formValues['variant'])));
        }

        try {
            $formValues = $request->request->get('sylius_cart_item');

            $variant = $this->addItemToPrimaryWishlist($formValues['variant']);

            if ($request->isXmlHttpRequest()) {
                if ($variant) {
                    $priceFormatter = $this->get('reiss.price_formatter');
                    $productImageHelper = $this->get('reiss.product.helper.image');
                    /** @var Product $product */
                    $product = $variant->getProduct();

                    return AjaxJsonResponse::success([
                        'name'    => $product->getName(),
                        'price'   => $priceFormatter->formatRoundedWithCurrency($product->getRetailPrice()),
                        'colour'  => $product->getColour()->getName(),
                        'size'    => $variant->getSize()->getPresentation(),
                        'image'   => $productImageHelper->getImagePath($product),
                    ]);
                } else {
                    return AjaxJsonResponse::error($this->get('translator')->trans('reiss.frontend.wishlist.messages.already_database'));
                }
            }

            if ($variant) {
                $this->addTranslatedFlash('success', 'reiss.frontend.wishlist.messages.success');
            } else {
                $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.messages.already_database');
            }

            return $this->redirectToRefererOrRoute('reiss_wishlist_show', $request);

        } catch (\Exception $e) {
            $errorMessage = sprintf('Error adding item to your wishlist: %s', $e->getMessage());

            if ($request->isXmlHttpRequest()) {
                return AjaxJsonResponse::error($errorMessage);
            }

            $this->addTranslatedFlash('error', $errorMessage);

            return $this->redirectToRefererOrRoute('reiss_wishlist_show', $request);
        }
    }

    /**
     * @return RedirectResponse
     */
    public function addNewWishlistLastPositionAction(Request $request)
    {
        $wishlistManager = $this->get('reiss.entity_manager.wishlist');
        $wishlists       = $this->get('reiss.repository.wishlist')->getWishlistsByCustomer($this->getCustomer());

        $wishlistManager->createNewWishlistLastPosition($this->getCustomer(), count($wishlists));

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function changeNameWishlistAction(Request $request)
    {
        $wishlistManager    = $this->get('reiss.manager.wishlist');
        $wishlistRepository = $this->get('reiss.repository.wishlist');

        $newName    = $request->get('newName');
        $wishlistId = $request->get('wishlistId');

        $wishlist = $wishlistRepository->findOneById($wishlistId);

        if (!$wishlist) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.wishlist_not_found');
        } elseif (!$newName) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.wishlist_no_name');
        } else {
            $wishlist->setName($newName);
            $wishlistManager->flush();

            $this->addTranslatedFlash('success', 'reiss.frontend.wishlist.alerts.new_name_wishlist', array('%name%' => $newName));
        }

        if ($request->isXmlHttpRequest()) {
            $response = $this->renderView('ReissWishlistBundle:Frontend:alerts.html.twig');

            return new Response($response);
        }

        return $this->redirect($request->headers->get('referer',
            array('wishlists' => $wishlistRepository->getWishlistsByCustomer($this->getCustomer()))
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function moveWishlistItemAction(Request $request)
    {
        $wishlistId     = $request->query->get('wishlistId');
        $wishlistItemId = $request->query->get('wishlistItemId');
        $session        = $request->getSession();
        $wishlist       = $this->get('reiss.repository.wishlist')->findOneById($wishlistId);
        $wishlistItem   = $this->get('reiss.repository.wishlist_item')->findOneById($wishlistItemId);

        $wishlistItem->setWishlist($wishlist);
        $this->get('reiss.entity_manager.wishlist_item')->persistAndFlush($wishlistItem);

        $session->getFlashBag()->add('success', $this->get('translator')
            ->trans('reiss.frontend.wishlist.alerts.moved_to_wishlist', array('%wishlistName%' => $wishlist->getName())));

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeWishlistItemAction(Request $request)
    {
        $wishlistItemRepository = $this->get('reiss.repository.wishlist_item');

        if (!$wishlistItemId = $request->get('wishlistItemId')) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.wishlist_variant_not_removed');
        } elseif (!$wishlistItem = $wishlistItemRepository->findOneById($wishlistItemId)) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.wishlist_variant_not_removed');
        } else {
            $wishlistItemManager = $this->get('reiss.entity_manager.wishlist_item');
            $wishlist            = $wishlistItem->getWishlist();
            $variant             = $wishlistItem->getVariant();
            $wishlistItem        = $wishlistItemRepository->getWishlistItemByVariant($wishlist, $variant);
            $wishlistItemManager->removeAndFlush($wishlistItem);

            $this->addTranslatedFlash('success', 'reiss.frontend.wishlist.alerts.wishlist_product_removed');
        }

        return $this->redirectToRoute('reiss_wishlist_show');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeWishlistAction(Request $request)
    {
        $wishlistRepository = $this->get('reiss.repository.wishlist');

        if (!$wishlistId = $request->get('wishlistId')) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.wishlist_not_removed');
        } elseif (!$wishlist = $wishlistRepository->findOneById($wishlistId)) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.wishlist_not_removed');
        } else {
            $wishlistManager = $this->get('reiss.entity_manager.wishlist');
            $wishlistManager->removeAndFlush($wishlist);
            $wishlistManager->ensurePrimaryWishlistIfExists($this->getCustomer());
            $this->addTranslatedFlash('success', 'reiss.frontend.wishlist.alerts.wishlist_removed');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addBasketToWishlistAction(Request $request)
    {
        $wishlist        = $this->get('reiss.repository.wishlist')->getPrimaryWishlist($this->getCustomer());
        $wishlistManager = $this->get('reiss.entity_manager.wishlist');
        $orderRepository = $this->get('sylius.repository.order');

        if (!$orderId = $request->get('orderId')) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.cart_not_added');
        } elseif (!$order = $orderRepository->findOneById($orderId)) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.cart_not_added');
        } elseif (OrderInterface::CHECKOUT_STATE_CART != $order->getState()) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.cart_not_added');
        } else {
            $messageAndParameter = $wishlistManager->insertWishlistItems($wishlist, $order);

            $this->addTranslatedFlash('success',
                $messageAndParameter['toTranslate'],
                array('%items%' => $messageAndParameter['numVariantsAdded'])
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @return JsonResponse
     */
    public function summaryJsonAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $customer = $this->getCustomer();

        $wishlistProducts = array();

        if ($customer) {
            $router                 = $this->get('router');
            $productImageHelper     = $this->get('reiss.product.helper.image');
            $wishlistItemRepository = $this->get('reiss.repository.wishlist_item');
            $priceFormatter         = $this->get('reiss.price_formatter');

            $wishlistItems = $wishlistItemRepository->getProductsByCustomer($customer);

            foreach ($wishlistItems as $wishlistItem) {
                $productVariant = $wishlistItem->getVariant();
                /** @var Product $product */
                $product        = $productVariant->getProduct();

                $wishlistProduct['id']             = $product->getId();
                $wishlistProduct['name']           = $product->getName();
                $wishlistProduct['url']            = $router->generate($product);
                $wishlistProduct['color']          = $product->getColour()->getName();
                $wishlistProduct['price']          = $priceFormatter->formatRoundedWithCurrency($product->getRetailPrice());
                $wishlistProduct['image']          = $productImageHelper->getImagePath($product);
                $wishlistProduct['stockIndicator'] = $productVariant->getOnHand();
                $wishlistProduct['created']        = $wishlistItem->getCreatedAt()->format('d-m-Y');

                $wishlistProducts[] = $wishlistProduct;
            }
        }

        $view = View::create()->setData($wishlistProducts);

        return $this->viewHandler->handle($configuration, $view);
    }

    // -------------------------------------- WISHLIST SHARING METHODS ----------------------------------------

    /**
     * @param string $shareToken
     *
     * @return Response
     */
    public function showSharedWishlistAction($shareToken)
    {
        if (!$shareToken) {
            $this->addTranslatedFlash('error', 'reiss.frontend.wishlist.alerts.empty_share_token');
        }

        if (!$wishlist = $this->get('reiss.repository.wishlist')->findOneBy(array('shareToken' => $shareToken))) {
            return $this->render('ReissWishlistBundle:Frontend/Wishlist:notFound.html.twig');
        }

        return $this->render('ReissWishlistBundle:Frontend/Wishlist:sharedView.html.twig', array('wishlist' => $wishlist));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function unshareWishlistAction(Request $request)
    {
        $wishlistRepository = $this->get('reiss.repository.wishlist');
        $wishlistManager    = $this->get('reiss.entity_manager.wishlist');

        if (!$wishlistId = $request->get('wishlistId')) {
            return $this->ajaxJsonErrorOrRefererRedirect($request, 'reiss.frontend.wishlist.alerts.empty_id');
        }
        if (!$wishlist = $wishlistRepository->findOneById($wishlistId)) {
            return $this->ajaxJsonErrorOrRefererRedirect($request, 'reiss.frontend.wishlist.alerts.empty_id');
        }

        $wishlistManager->removeShareToken($wishlist);
        $message = $this->get('translator')->trans('reiss.frontend.wishlist.alerts.wishlist_unshared');

        if ($request->isXmlHttpRequest()) {
            return AjaxJsonResponse::success([], $message);
        }

        $this->addFlash('success', $message);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function shareWishlistAction(Request $request)
    {
        $wishlistRepository = $this->get('reiss.repository.wishlist');
        $wishlistManager    = $this->get('reiss.entity_manager.wishlist');

        if (!$wishlistId = $request->get('wishlistId')) {
            return $this->ajaxJsonErrorOrRefererRedirect($request, 'reiss.frontend.wishlist.alerts.empty_id');
        }
        if (!$wishlist = $wishlistRepository->findOneById($wishlistId)) {
            return $this->ajaxJsonErrorOrRefererRedirect($request, 'reiss.frontend.wishlist.alerts.empty_id');
        }

        // @TODO dispatch the Wishlist Shared email event in here when the wishlist is back in business
        $token = $wishlistManager->createShareToken($wishlist);

        $url = $this->get('router')->generate('reiss_wishlist_shared_wishlist', array('shareToken' => $token), true);
        $message = $this->get('translator')->trans('reiss.frontend.wishlist.alerts.wishlist_share', array('%url%' => $url));

        if ($request->isXmlHttpRequest()) {
            return AjaxJsonResponse::success([], $message);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getSharedWishlistUrlAction(Request $request)
    {
        $wishlistId = $request->get('wishlistId');
        /** @var Wishlist $wishlist */
        $wishlist = $this->get('reiss.repository.wishlist')->findOneById($wishlistId);

        if (!$wishlist->getShareToken()) {
            return AjaxJsonResponse::error($this->get('translator')->trans('reiss.frontend.wishlist.no-url-share'));
        }

        return AjaxJsonResponse::success([], $this->get('translator')->trans(
            'reiss.frontend.wishlist.share',
            array('%url%' => $wishlist->getShareToken())
        ));
    }

    // -------------------------------------- BACKEND / ADMIN ACTIONS -------------------------------------------

//    /**
//     * @param Request $request
//     *
//     * @return Response
//     */
//    public function statisticsAction(Request $request)
//    {
//        $this->isReissGrantedOr403($request, 'statistics');
//
//        $this->get('reiss.wishlist_statistics.helper')->getMaleStatistics(Customer::MALE_GENDER, null, null);
//
//        /* TODO: Waiting to implement them
//         * Zone will use ZoneForm to check in the query
//         */
////        $productType = $request->get('reiss_wishlist_filter')['product_type'];
//
////      Now is your turn Fernandou, this should be about channels, not zones
////        $zones = array();
////        $zone = $this->get('sylius.repository.zone')->find($request->get('reiss_wishlist_filter')['zone']);
//
//        $gender      = $request->get('reiss_wishlist_filter')['gender'];
//        $seasonId    = $request->get('reiss_wishlist_filter')['season'];
//        $firstSeason = $this->get('reiss.repository.season')->findAll();
//        $season      = $this->get('reiss.repository.season')->findOneById($seasonId);
//        $form        = $this->createForm(new StatisticsFilterType());
//
//        $form->handleRequest($request);
//
//        //Default values (Waiting for Product type)
//        if (!$request->get('reiss_wishlist_filter')) {
//            $form->get('season')->setData($firstSeason[0]->getId());
//        } else {
//            $form->get('gender')->setData($gender);
//            $form->get('season')->setData($season);
//        }
//
//        $genders = array_keys($form->get('gender')->getConfig()->getOption('choices'));
//
//        $array         = $this->getStatistics($form->get('gender')->getData(), $form->get('season')->getData(), $genders);
//        $array['form'] = $form->createView();
//
//        return $this->render('ReissWishlistBundle:Backend:index.html.twig', $array);
//    }
//
//    /**
//     * TODO: MOST OF THIS CODE SHOULD BE MOVED INTO WishlistItemRepository AND THIS METHOD MERGED ABOVE
//     *
//     * @param String $gender
//     * @param Season $season
//     *
//     * @return array
//     */
//    private function getStatistics($gender, $season, array $genders)
//    {
//        $topProductsArray = array();
//
//        foreach ($genders as $g) {
//            $stockProductsArray['stockCount'][$g] = array('stock' => 0, 'outOfStock' => 0);
//        }
//
//        $wishlistRepo     = $this->get('reiss.repository.wishlist');
//        $wishlistItemRepo = $this->get('reiss.repository.wishlist_item');
//
//        $topProducts = $this->get('reiss.repository.wishlist_item')->filterTopProducts($gender, $season);
//
//        /*  TODO: Waiting for Adrian's pull request approved. This will be in WishlistItemRepository
//                foreach ($wishlistRepo->getProductsByWishlist() as $product) {
//                    if (!$product->isOutOfStock() && $product->getGender() == "M") {
//                        $stockProductsArray['stockCount'][$product->getGender()]['stock']++;
//                    } elseif ($product->isOutOfStock() && $product->getGender() == "M") {
//                        $stockProductsArray['stockCount'][$product->getGender()]['outOfStock']++;
//                    } elseif (!$product->isOutOfStock() && $product->getGender() == "W") {
//                        $stockProductsArray['stockCount'][$product->getGender()]['stock']++;
//                    } elseif ($product->isOutOfStock() && $product->getGender() == "W") {
//                        $stockProductsArray['stockCount'][$product->getGender()]['outOfStock']++;
//                    }
//                }
//        */
//
//        foreach (array_slice($topProducts, 0, 50) as $resultQuery) {
//            $countProductArray['product'] = $this->get('sylius.repository.product')->findOneById($resultQuery['id']);
//            $countProductArray['count']   = $resultQuery[1];
//
//            $topProductsArray[] = $countProductArray;
//        }
//
//        $summary = array(
//            'wishlistCount' => $wishlistRepo->getWishlistCountsByGender(),
//            'customerCount' => $wishlistRepo->getCustomerWithWishlistsCountsByGender(),
//            'sale'          => $wishlistItemRepo->getProductCountByGenderAndMarkdown(),
//            'stock'         => $stockProductsArray,
//            'translation'   => $gender,
//            'topProducts'   => $topProductsArray
//        );
//
//        return $summary;
//    }

    /**
     * @param int $id
     *
     * @return ProductVariant|null
     */
    private function addItemToPrimaryWishlist($id)
    {
        $variant = $this->get('sylius.repository.product_variant')->find($id);

        // At this point we have a Customer as it has passed the IS_AUTHENTICATED_REMEMBERED check or it has gone
        // through the protected route
        $customer = $this->getCustomer();

        if ($this->get('reiss.entity_manager.wishlist')->addItemToPrimaryWishlist($customer, $variant)) {
            return $variant;
        }
    }

    /**
     * @param Request $request
     * @param integer $wishlistItemId
     *
     * @return Response
     * @throws WishlistItemUnavailableException
     */
    public function displayProductDetailWishlistAction(Request $request, $wishlistItemId)
    {
        $wishlistItem = $this->get('reiss.repository.wishlist_item')->find($wishlistItemId);

        if (!$wishlistItem) {
            throw new WishlistItemUnavailableException();
        }

        // if another productId is passed through (to switch colour within the overlay) then load that
        if ($productId = $request->query->get('productId')) {
            $product = $this->get('sylius.repository.product')->find($productId);
            $variant = null;
        } else {
            $product = $wishlistItem->getVariant()->getProduct();
            $variant = $wishlistItem->getVariant();
        }

        $wishlists    = $this->get('reiss.repository.wishlist')->getWishlistsByCustomer($this->getUser());
        $wishlistForm = new WishlistChoiceType($this->getUser()->getCustomer(), $wishlistItem->getWishlist(), $this->get('translator'));

        $templateVars = [
            'product'               => $product,
            'variant'               => $variant,
            'productColourVersions' => $this->get('sylius.repository.product')->findAvailableColourVersions($product),
            'wishlists'             => $wishlists,
            'wishlistItem'          => $wishlistItem,
            'wishlistForm'          => $this->createForm($wishlistForm)->createView(),
        ];

        if ($request->isXmlHttpRequest()) {
            return AjaxJsonResponse::info(
                $this->renderView('ReissWishlistBundle:Frontend/Overlays:wishlistItemDetailOverlay.html.twig', $templateVars)
            );
        }

        return $this->render('ReissWishlistBundle:Frontend/Overlays:wishlistItemDetailOverlayMobile.html.twig', $templateVars);
    }
}