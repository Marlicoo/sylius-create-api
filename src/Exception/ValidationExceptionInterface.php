<?php

namespace App\Exception;


use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationExceptionInterface
{
    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface;
}
