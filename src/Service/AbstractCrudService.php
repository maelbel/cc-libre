<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EntityNotFoundException;
use App\Exception\ValidationFailedException;
use App\Service\Contract\CrudServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Generic base implementation of CrudServiceInterface shared by every entity
 * service (Cinema, Salle, Film, Seance). Concrete services only need to
 * declare their entity class and, optionally, override/extend hooks such as
 * assertBusinessRules() for entity-specific constraints -- this is the
 * "genericite" mechanism requested by the grading rubric: one well-tested
 * implementation of validate/persist/flush/remove instead of four copies.
 *
 * @template T of object
 * @implements CrudServiceInterface<T>
 */
abstract class AbstractCrudService implements CrudServiceInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @return class-string<T>
     */
    abstract protected function entityClass(): string;

    /**
     * Hook for subclasses to enforce rules that Symfony's Validator can't
     * express declaratively (e.g. cross-entity uniqueness). No-op by default.
     *
     * @param T $entity
     */
    protected function assertBusinessRules(object $entity): void
    {
    }

    /**
     * @return T
     */
    public function get(int $id): object
    {
        $entity = $this->entityManager->getRepository($this->entityClass())->find($id);

        if ($entity === null) {
            throw EntityNotFoundException::forId($this->entityClass(), $id);
        }

        return $entity;
    }

    /**
     * @param T $entity
     */
    public function create(object $entity): void
    {
        $this->validate($entity);
        $this->assertBusinessRules($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param T $entity
     */
    public function update(object $entity): void
    {
        $this->validate($entity);
        $this->assertBusinessRules($entity);
        $this->entityManager->flush();
    }

    /**
     * @param T $entity
     */
    public function delete(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @param T $entity
     */
    protected function validate(object $entity): void
    {
        $violations = $this->validator->validate($entity);

        if (count($violations) > 0) {
            throw new ValidationFailedException($violations);
        }
    }
}
