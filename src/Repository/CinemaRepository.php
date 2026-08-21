<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cinema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cinema>
 */
class CinemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cinema::class);
    }

    /**
     * Query builder for the admin CRUD list, filterable by a free-text search
     * on nom/ville, ready to be handed to KnpPaginator.
     */
    public function findForAdminList(?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')->orderBy('c.nom', 'ASC');

        return $this->applySearch($qb, $search);
    }

    /**
     * Query builder for the public homepage (same search, public-friendly name).
     */
    public function findForPublicList(?string $search = null): QueryBuilder
    {
        return $this->findForAdminList($search);
    }

    private function applySearch(QueryBuilder $qb, ?string $search): QueryBuilder
    {
        if ($search !== null && trim($search) !== '') {
            $qb->andWhere('c.nom LIKE :search OR c.ville LIKE :search')
                ->setParameter('search', '%'.trim($search).'%');
        }

        return $qb;
    }
}
