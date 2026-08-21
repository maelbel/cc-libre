<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Repository\FilmRepository;
use App\Service\Contract\FilmServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends AbstractCrudService<Film>
 */
class FilmService extends AbstractCrudService implements FilmServiceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        private readonly FilmRepository $filmRepository,
    ) {
        parent::__construct($entityManager, $validator);
    }

    protected function entityClass(): string
    {
        return Film::class;
    }

    public function search(?string $query): QueryBuilder
    {
        return $this->filmRepository->findForAdminList($query);
    }

    public function findProgrammesAujourdhui(Cinema $cinema): array
    {
        return $this->filmRepository->findProgrammesAujourdhuiPourCinema((int) $cinema->getId());
    }
}
