<?php

namespace App\UseCase\Command\Ecommerce;


use App\Exception\Ecommerce\CreateWebsiteValidationExceptionInterface;
use League\Tactician\Middleware;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateWebsiteMiddleware implements Middleware
{
    /** @var ValidatorInterface */
    private $validator;
    /**
     * ValidationMiddleware constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $command
     * @param callable $next
     * @return mixed
     * @throws CreateWebsiteValidationExceptionInterface
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof CreateWebsite) {

            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->validator->validate($command);

            if (\count($errors)) {
                throw new CreateWebsiteValidationExceptionInterface($errors);
            }
        }

        return $next($command);
    }

}
