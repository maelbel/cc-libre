<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }

    public function findForAdminList(?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.film', 'f')->addSelect('f')
            ->join('s.salle', 'salle')->addSelect('salle')
            ->join('salle.cinema', 'c')->addSelect('c')
            ->join('s.horaire', 'h')->addSelect('h')
            ->orderBy('s.date', 'DESC');

        if ($search !== null && trim($search) !== '') {
            $qb->andWhere('f.titre LIKE :search OR c.nom LIKE :search')
                ->setParameter('search', '%'.trim($search).'%');
        }

        return $qb;
    }

    /**
     * Seances scheduled today for a given cinema, grouped implicitly by film
     * through the ORDER BY (used by the public "cinema detail" page).
     *
     * @return Seance[]
     */
    public function findAujourdhuiPourCinemaEtFilm(Cinema $cinema, Film $film): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('s')
            ->join('s.salle', 'salle')->addSelect('salle')
            ->join('s.horaire', 'h')->addSelect('h')
            ->andWhere('salle.cinema = :cinema')
            ->andWhere('s.film = :film')
            ->andWhere('s.date = :today')
            ->setParameter('cinema', $cinema)
            ->setParameter('film', $film)
            ->setParameter('today', $today)
            ->orderBy('h.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * All seances available today across every cinema (used by the public
     * homepage "seances disponibles dans chaque cinema").
     *
     * @return Seance[]
     */
    public function findDisponiblesAujourdhui(): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('s')
            ->join('s.film', 'f')->addSelect('f')
            ->join('s.salle', 'salle')->addSelect('salle')
            ->join('salle.cinema', 'c')->addSelect('c')
            ->join('s.horaire', 'h')->addSelect('h')
            ->andWhere('s.date = :today')
            ->setParameter('today', $today)
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('h.heureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
