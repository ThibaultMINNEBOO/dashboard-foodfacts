<?php

namespace App\Domain\HttpClient\Exception;

class ApiUnreachableException extends \Exception
{
    public function __construct(string $message = "The API is unreachable.", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
