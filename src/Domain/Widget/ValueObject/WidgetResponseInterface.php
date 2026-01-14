<?php

namespace App\Domain\Widget\ValueObject;

interface WidgetResponseInterface
{
    public function isSuccess(): bool;
    public function getResult(): int|string|float;
}
