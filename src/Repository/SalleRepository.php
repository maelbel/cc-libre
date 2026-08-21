<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cinema;
use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Salle>
 */
class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }

    /**
     * Admin CRUD list, filterable by search on the cinema name and optionally
     * scoped to one cinema.
     */
    public function findForAdminList(?string $search = null, ?Cinema $cinema = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.cinema', 'c')->addSelect('c')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('s.numero', 'ASC');

        if ($cinema !== null) {
            $qb->andWhere('s.cinema = :cinema')->setParameter('cinema', $cinema);
        }

        if ($search !== null && trim($search) !== '') {
            $qb->andWhere('c.nom LIKE :search')->setParameter('search', '%'.trim($search).'%');
        }

        return $qb;
    }
}
