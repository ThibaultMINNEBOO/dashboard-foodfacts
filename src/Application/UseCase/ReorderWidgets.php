<?php

namespace App\Application\UseCase;

use App\Domain\Repository\WidgetRepositoryInterface;

class ReorderWidgets
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    ) {
    }

    public function execute(array $data): void
    {
        foreach ($data['order'] as $position => $id) {
            $widget = $this->widgetRepository->findById((int)$id);
            if ($widget) {
                $widget->setPosition((int)$position + 1);
                $this->widgetRepository->save($widget);
            }
        }
    }
}
