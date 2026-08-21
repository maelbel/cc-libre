<?php

declare(strict_types=1);

namespace App\Service\Contract;

/**
 * Generic contract implemented by every admin CRUD service.
 *
 * Depending on this interface (Dependency Inversion Principle) rather than on
 * concrete service classes lets controllers and tests swap in fakes/mocks
 * without touching business code.
 *
 * @template T of object
 */
interface CrudServiceInterface
{
    /**
     * @return T
     */
    public function get(int $id): object;

    /**
     * @param T $entity
     */
    public function create(object $entity): void;

    /**
     * @param T $entity
     */
    public function update(object $entity): void;

    /**
     * @param T $entity
     */
    public function delete(object $entity): void;
}
