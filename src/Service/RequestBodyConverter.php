<?php

namespace App\Service;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestBodyConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        $parameters = json_decode($request->getContent(), true);

        $className = $configuration->getClass();
        $command = new $className($parameters['companyName'], $parameters['email'], $parameters['logoUrl'], $parameters['companySubtitle']);

        $request->attributes->set($configuration->getName(), $command);
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === 'App\UseCase\Command\Ecommerce\CreateWebsite';
    }
}
