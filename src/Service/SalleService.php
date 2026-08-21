<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cinema;
use App\Entity\Salle;
use App\Exception\DuplicateEntityException;
use App\Repository\SalleRepository;
use App\Service\Contract\SalleServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends AbstractCrudService<Salle>
 */
class SalleService extends AbstractCrudService implements SalleServiceInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        private readonly SalleRepository $salleRepository,
    ) {
        parent::__construct($entityManager, $validator);
    }

    protected function entityClass(): string
    {
        return Salle::class;
    }

    public function search(?string $query, ?Cinema $cinema = null): QueryBuilder
    {
        return $this->salleRepository->findForAdminList($query, $cinema);
    }

    /**
     * Business rule not expressible with a single declarative Assert: a room
     * number must be unique within its cinema (enforced in DB by a unique
     * index too, this gives a friendly error instead of a raw SQL exception).
     */
    protected function assertBusinessRules(object $entity): void
    {
        if (!$entity instanceof Salle) {
            return;
        }

        $existing = $this->salleRepository->findOneBy([
            'cinema' => $entity->getCinema(),
            'numero' => $entity->getNumero(),
        ]);

        if ($existing !== null && $existing->getId() !== $entity->getId()) {
            throw new DuplicateEntityException(sprintf(
                'La salle n°%d existe deja pour le cinema "%s".',
                $entity->getNumero(),
                $entity->getCinema()?->getNom()
            ));
        }
    }
}
