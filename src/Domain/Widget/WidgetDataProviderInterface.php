<?php

namespace App\Domain\Widget;

use App\Domain\Enums\WidgetType;
use App\Domain\Widget\ValueObject\WidgetResponseInterface;

interface WidgetDataProviderInterface
{
    public function supports(WidgetType $type): bool;

    public function getData(array $configuration): WidgetResponseInterface;
}
