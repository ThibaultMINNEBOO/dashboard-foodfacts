<?php

namespace App\Application\UseCase;

use App\Domain\Entity\Dashboard;
use App\Domain\Entity\Widget;
use App\Domain\Repository\WidgetRepositoryInterface;
use App\Domain\Widget\ValueObject\WidgetResponseInterface;
use App\Domain\Widget\WidgetDataProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class LoadAllWidgets
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    ) {
    }

    public function execute(Dashboard $dashboard): array
    {
        return $this->widgetRepository->findAll($dashboard);
    }
}
