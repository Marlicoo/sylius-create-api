<?php

namespace App\EventListener\Ecommerce;


use App\Exception\ValidationExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ValidationFailure
{
    /** @var  SerializerInterface */
    private $serializer;

    /**
     * ValidationFailure constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if (!$exception instanceof ValidationExceptionInterface) {
            return;
        }

        $errors = [];

        foreach ($exception->getErrors() as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        $responseData = ['success' => false, 'error' => 'Validation failure', 'validationErrors' => $errors];

        $event->setResponse(new JsonResponse($this->serializer->serialize($responseData, 'json'),
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY, [], true));
    }
}
