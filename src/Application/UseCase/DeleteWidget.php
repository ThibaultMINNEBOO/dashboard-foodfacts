<?php

namespace App\Application\UseCase;

use App\Domain\Entity\Widget;
use App\Domain\Repository\WidgetRepositoryInterface;

class DeleteWidget
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    ) {
    }

    public function execute(Widget $widget): void
    {
        $this->widgetRepository->delete($widget);
    }
}
