<?php

namespace App\Domain\Widget\ValueObject;

class WidgetError implements WidgetResponseInterface
{
    private string $message;

    public static function create(string $message): self
    {
        $instance = new self();
        $instance->message = $message;
        return $instance;
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function getResult(): int|string|float
    {
        return $this->message;
    }
}
