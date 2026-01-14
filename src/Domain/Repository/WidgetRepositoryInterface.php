<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Dashboard;
use App\Domain\Entity\Widget;

interface WidgetRepositoryInterface
{
    public function save(Widget $entity): void;

    public function findAllOrderedByPosition(Dashboard $dashboard): array;

    public function findById(int $id): ?Widget;

    public function delete(Widget $widget): void;
}
