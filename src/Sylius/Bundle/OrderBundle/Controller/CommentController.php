<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Order comment controller.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class CommentController extends ResourceController
{
    /**
     * Create new comment associated to order.
     */
    public function createAction(Request $request)
    {
        if (!$order = $this->container->get('sylius.repository.order')->findOneById($request->get('id'))) {
            throw new NotFoundHttpException('Requested order does not exist');
        }

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $resource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource->setOrder($order);
            $resource->setAuthor($this->getUser()->getEmail());

            $this->repository->add($resource);

            // Technically I've broken this route in Sylius but I can't be bothered to override this entire controller
            // in our project just to change it... No one will be using our Sylius fork outside of Reiss.  PW 06/2016
            return $this->redirectHandler->redirectToRoute($configuration, 'sylius_backend_order_show', ['number' => $order->getNumber()]);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(View::create($form, 400));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('create.html'))
            ->setData([
                $this->metadata->getName() => $resource,
                'form' => $form->createView(),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
