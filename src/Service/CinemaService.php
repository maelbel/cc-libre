<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cinema;
use App\Repository\CinemaRepository;
use App\Service\Contract\CinemaServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends AbstractCrudService<Cinema>
 */
class CinemaService extends AbstractCrudService implements CinemaServiceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        private readonly CinemaRepository $cinemaRepository,
    ) {
        parent::__construct($entityManager, $validator);
    }

    protected function entityClass(): string
    {
        return Cinema::class;
    }

    public function search(?string $query): QueryBuilder
    {
        return $this->cinemaRepository->findForAdminList($query);
    }
}
