<?php

namespace App\Exception\Ecommerce;


use App\Exception\ValidationExceptionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class CreateWebsiteValidationExceptionInterface extends \InvalidArgumentException implements ValidationExceptionInterface
{
    /** @var  ConstraintViolationListInterface */
    private $errors;
    public function __construct($errors, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
