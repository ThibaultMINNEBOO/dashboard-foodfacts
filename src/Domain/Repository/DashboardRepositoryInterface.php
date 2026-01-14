<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Dashboard;

interface DashboardRepositoryInterface
{
    public function save(Dashboard $dashboard): void;
}
