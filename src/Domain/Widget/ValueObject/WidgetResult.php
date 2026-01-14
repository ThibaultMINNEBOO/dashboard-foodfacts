<?php

namespace App\Domain\Widget\ValueObject;

class WidgetResult implements WidgetResponseInterface
{
    private int|string|float $result;

    public static function create(int|string|float $result): self
    {
        $instance = new self();
        $instance->result = $result;
        return $instance;
    }

    public function isSuccess(): bool
    {
        return true;
    }

    public function getResult(): int|string|float
    {
        return $this->result;
    }
}
