<?php
/**
 * @author    Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 * @date      17/04/15
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\StoreBundle\Controller;

use Reiss\UserBundle\Entity\Customer;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
class ReissBaseController extends ResourceController
{
    /**
     * A slightly bastardised version of this for the updated ResourceController
     *
     * @param Request $request
     * @param string  $permission
     *
     * @return RequestConfiguration
     *
     * @throws AccessDeniedException
     */
    protected function isReissGrantedOr403(Request $request, $permission)
    {
        // Wrap around this compared to sylius so we only need to pass the Request
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$configuration->hasPermission()) {
            return $configuration;
        }

        // This allows full-path permissions either through a reiss namespace or if we want to call
        // this method outside of the resource namespace (with a separate dedicated controller)
        if (false === strpos($permission, 'reiss.') && false === strpos($permission, 'sylius.')) {
            $permission = $configuration->getPermission($permission);
        }

        if (!$this->authorizationChecker->isGranted($configuration, $permission)) {
            throw new AccessDeniedException();
        }

        return $configuration;
    }

    /**
     * @param Request $request
     * @param string  $message
     * @param bool    $translate
     *
     * @return RedirectResponse
     */
    protected function ajaxJsonErrorOrRefererRedirect(Request $request, $message, $translate = true)
    {
        if ($translate) {
            $message = $this->get('translator')->trans($message);
        }

        if ($request->isXmlHttpRequest()) {
            return AjaxJsonResponse::error($message);
        }

        $this->addFlash('error', $message);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param string  $routeName
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function redirectToRefererOrRoute($routeName, Request $request)
    {
        if ($request->headers->get('referer')) {
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->redirectToRoute($routeName);
    }

    /**
     * @param string $type
     * @param string $textToTranslate
     * @param array  $parameters
     *
     * @return string
     */
    protected function addTranslatedFlash($type, $textToTranslate, $parameters = array())
    {
        $translatedText = $this->get('translator')->trans($textToTranslate, $parameters);

        $this->addFlash($type, $translatedText);

        return $translatedText;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        if (!$this->getUser()) {
            return null;
        }

        return $this->getUser()->getCustomer();
    }
}