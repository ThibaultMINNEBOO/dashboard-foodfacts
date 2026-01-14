<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Dashboard;
use App\Domain\Entity\Widget;
use App\Domain\Repository\WidgetRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Widget>
 */
class WidgetRepository extends ServiceEntityRepository implements WidgetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Widget::class);
    }

    public function save(Widget $widget): void
    {
        $this->getEntityManager()->persist($widget);
        $this->getEntityManager()->flush();
    }

    public function findAllOrderedByPosition(Dashboard $dashboard): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.dashboard = :dashboard')
            ->setParameter('dashboard', $dashboard)
            ->orderBy('w.position')
            ->addOrderBy('w.id')
            ->getQuery()
            ->getResult();
    }

    public function findById(int $id): ?Widget
    {
        return $this->find($id);
    }

    public function delete(Widget $widget): void
    {
        $this->getEntityManager()->remove($widget);
        $this->getEntityManager()->flush();
    }
}
