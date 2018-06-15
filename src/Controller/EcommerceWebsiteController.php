<?php

namespace App\Controller;

use App\Service\TemplateCreator;
use App\UseCase\Ecommerce\CreateShop;
use App\UseCase\Ecommerce\CreateShopHandler;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class EcommerceWebsiteController extends Controller
{
    /**
     * #TODO 1. Create write model : new shop
     * #TODO 2. Create read model
     * #TODO 3. DI
     * @param CommandBus $commandBus
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/online-shop", name="api_online_shop_create")
     * @Method("POST")
     */
    public function createShopAction(CommandBus $commandBus, Request $request, TemplateCreator $templateCreator)
    {
        $companyName = (string)$request->request->get('companyName');
        $email = (string)$request->request->get('email');
        $logoUrl = (string)$request->request->get('logoUrl');

        $command = new CreateShop($companyName, $email, $logoUrl);
        $commandBus->handle($command);

        return new JsonResponse('success', JsonResponse::HTTP_CREATED);
    }
}
