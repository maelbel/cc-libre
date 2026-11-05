<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Seance;
use App\Repository\SeanceRepository;
use App\Service\Contract\SeanceServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends AbstractCrudService<Seance>
 */
class SeanceService extends AbstractCrudService implements SeanceServiceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        private readonly SeanceRepository $seanceRepository,
    ) {
        parent::__construct($entityManager, $validator);
    }

    protected function entityClass(): string
    {
        return Seance::class;
    }

    public function search(?string $query): QueryBuilder
    {
        return $this->seanceRepository->findForAdminList($query);
    }

    public function findAujourdhuiPourCinemaEtFilm(Cinema $cinema, Film $film): array
    {
        return $this->seanceRepository->findAujourdhuiPourCinemaEtFilm($cinema, $film);
    }

    public function findDisponiblesAujourdhui(): array
    {
        return $this->seanceRepository->findDisponiblesAujourdhui();
    }

    /**
     * A room can only host one screening at a given date/time slot. This
     * cannot be a simple unique DB index because it depends on the Horaire's
     * time range, not just its id, so it lives here as a business rule.
     */
    protected function assertBusinessRules(object $entity): void
    {
        if (!$entity instanceof Seance) {
            return;
        }

        $horaire = $entity->getHoraire();

        // Compared by time range, not by Horaire id: a brand-new Seance's
        // Horaire has no id yet (persisted only on flush, via cascade), and
        // two distinct Horaire rows sharing the same range are still a
        // conflict for the same salle/date.
        $conflicts = $this->seanceRepository->createQueryBuilder('s')
            ->innerJoin('s.horaire', 'h')
            ->andWhere('s.salle = :salle')
            ->andWhere('s.date = :date')
            ->andWhere('h.heureDebut = :heureDebut')
            ->andWhere('h.heureFin = :heureFin')
            ->setParameter('salle', $entity->getSalle())
            ->setParameter('date', $entity->getDate())
            ->setParameter('heureDebut', $horaire?->getHeureDebut())
            ->setParameter('heureFin', $horaire?->getHeureFin())
            ->getQuery()
            ->getResult();

        foreach ($conflicts as $conflict) {
            if ($conflict->getId() !== $entity->getId()) {
                throw new \App\Exception\DuplicateEntityException(
                    'Cette salle est deja reservee pour ce creneau horaire a cette date.'
                );
            }
        }
    }
}
