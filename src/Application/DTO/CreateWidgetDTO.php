<?php

namespace App\Application\DTO;

use App\Domain\Enums\WidgetType;

class CreateWidgetDTO
{
    public function __construct(
        public readonly string $title,
        public readonly WidgetType $type,
        public readonly ?string $country,
        public readonly ?string $nutriscore
    ) {
    }
}
