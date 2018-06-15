<?php

namespace App\Controller;

use App\Entity\User;
use App\UseCase\Ecommerce\CreateWebsite;
use Doctrine\ORM\EntityManagerInterface;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;


class EcommerceWebsiteController extends Controller
{
    /**
     * @param CommandBus $commandBus
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/ecommerce-websites", name="api_ecommerce_website_create")
     * @Method("POST")
     * @SWG\Response(
     *     response=201,
     *     description="Returns the register success set to true",
     *     @SWG\Schema(
     *        example= {
     *           "succes": true,
     *           "password": "cSVLOzzp4Z",
     *           "url": "buty.localhost:8131"
     *         }
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returns when params validation failure",
     *
     *     @SWG\Schema(
     *        example={
     *              "success": false,
     *              "error": "Ecommerce website has not been created"
     *              })
     * )
     * @SWG\Parameter(
     *     name="register_user",
     *     in="body",
     *     @Model(type=CreateWebsite::class)
     * )
     * @SWG\Tag(name="ecommerce-websites")
     */
    public function createShopAction(CommandBus $commandBus, Request $request, EntityManagerInterface $em)
    {
        try {
            $companyName = (string)$request->request->get('companyName');
            $email = (string)$request->request->get('email');
            $logoUrl = (string)$request->request->get('logoUrl');
            $companySubtitle = (string)$request->request->get('companySubtitle');

            if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
                return new JsonResponse(['success' => false, 'error' => 'Email has already been used'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $command = new CreateWebsite($companyName, $email, $logoUrl, $companySubtitle);
            $commandBus->handle($command);

            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$user) {
            return new JsonResponse(['success' => false, 'error' => 'Ecommerce website has not been created'], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['succes' => true, 'password' => $user->getPassword(), 'url' => $user->getEcommerceShop()->getUrl()], JsonResponse::HTTP_CREATED);
    }
}
