<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidParamsException extends \InvalidArgumentException
{
    private ConstraintViolationListInterface $errors;

    #[Pure] public function __construct(string $message, ConstraintViolationListInterface $errors, $code = 0, \Throwable $previous = null) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
