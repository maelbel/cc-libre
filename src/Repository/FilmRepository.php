<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    public function findForAdminList(?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f')->orderBy('f.titre', 'ASC');

        if ($search !== null && trim($search) !== '') {
            $qb->andWhere('f.titre LIKE :search OR f.genre LIKE :search')
                ->setParameter('search', '%'.trim($search).'%');
        }

        return $qb;
    }

    /**
     * Films that have at least one Seance today in the given cinema, used by
     * the public "cinema detail" page.
     *
     * @return Film[]
     */
    public function findProgrammesAujourdhuiPourCinema(int $cinemaId): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('f')
            ->join('f.seances', 's')
            ->join('s.salle', 'salle')
            ->andWhere('salle.cinema = :cinemaId')
            ->andWhere('s.date = :today')
            ->setParameter('cinemaId', $cinemaId)
            ->setParameter('today', $today)
            ->distinct()
            ->orderBy('f.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
