<?php

namespace App\Application\UseCase;

use App\Application\DTO\CreateWidgetDTO;
use App\Domain\Entity\Dashboard;
use App\Domain\Entity\Widget;
use App\Domain\Enums\WidgetType;
use App\Domain\Repository\WidgetRepositoryInterface;

class CreateWidget
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    ) {
    }

    public function execute(CreateWidgetDTO $createWidgetDTO, Dashboard $currentDashboard): void
    {
        $configuration = [];

        match($createWidgetDTO->type) {
            WidgetType::PRODUCT_COUNT_BY_COUNTRIES => $configuration['country'] = $createWidgetDTO->country,
            WidgetType::PRODUCT_COUNT_BY_NUTRISCORE => $configuration['nutriscore'] = $createWidgetDTO->nutriscore,
            default => null,
        };

        $currentWidgets = $this->widgetRepository->findAllOrderedByPosition($currentDashboard);

        $widget = Widget::create(
            $createWidgetDTO->title,
            $createWidgetDTO->type,
            count($currentWidgets)+1,
            $configuration
        )->withDashboard($currentDashboard);

        $this->widgetRepository->save($widget);
    }
}
