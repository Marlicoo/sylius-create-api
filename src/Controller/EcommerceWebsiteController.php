<?php

namespace App\Controller;

use App\UseCase\Command\Ecommerce\CreateWebsite;
use App\UseCase\Query\Ecommerce\WebsiteQuery;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class EcommerceWebsiteController extends Controller
{
    /** CommandBus $commandBus */
    private $commandBus;

    /**
     * EcommerceWebsiteController constructor.
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param CommandBus $commandBus
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/ecommerce-websites", name="api_ecommerce_website_create"),
     *
     * @Method("POST")
     * @SWG\Response(
     *     response=201,
     *     description="Returns the register success set to true",
     *     @SWG\Schema(
     *        example= {
     *            "succes": true,
     *            "login": "admin46@onwelo.pl",
     *            "password": "OmBSeaWjwW",
     *            "url": "http://name.localhost:8195",
     *            "adminUrl": "http://name.localhost:8195/admin"
     *         }
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returns when error",
     *
     *     @SWG\Schema(
     *        example={
     *              "success": false,
     *              "error": "Ecommerce website has not been created"
     *              })
     * )
     *
     * @SWG\Response(
     *     response=422,
     *     description="Returns when params validation failure",
     *
     *     @SWG\Schema(
     *        example={
     *              {
     *               "success": false,
     *               "error": "Validation failure",
     *               "validationErrors": {
     *                    "companyName": "No company name provided",
     *                    "email": "Invalid email address provided"
     *                    }
     *                 }
     *              })
     * )
     * @SWG\Parameter(
     *     name="register_user",
     *     in="body",
     *     @Model(type=CreateWebsite::class)
     * )
     * @SWG\Tag(name="ecommerce-websites")
     *
     * @ParamConverter("command", class="App\UseCase\Command\Ecommerce\CreateWebsite", converter="command_converter")
     */
    public function createEcommerceWebsite(Request $request, CreateWebsite $command, WebsiteQuery $websiteQuery)
    {
        if ($request->getContentType() !== 'json') {

            return new JsonResponse(['success' => false, 'error' => 'Unsupported content type ' . $request->getContentType()],
                JsonResponse::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        $this->commandBus->handle($command);

        $websiteView = $websiteQuery->getByUserEmail($command->getEmail());

        return new JsonResponse(['succes' => true, 'login' => $websiteView->getUserLogin(), 'password' => $websiteView->getUserPassword(),
            'url' => $websiteView->getUrl(), 'adminUrl' => $websiteView->getUrl() . '/admin'], JsonResponse::HTTP_CREATED);
    }
}

