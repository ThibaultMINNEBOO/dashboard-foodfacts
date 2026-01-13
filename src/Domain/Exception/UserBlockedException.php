<?php

namespace App\Domain\Exception;

class UserBlockedException extends \DomainException
{
    private function __construct(string $message, private string $email) {
        parent::__construct($message);
    }

    public static function create(string $email): self
    {
        return new self("Le compte '$email' est bloqué suite à trop de tentatives", $email);
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
