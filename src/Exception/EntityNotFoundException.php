<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Generic "not found" exception reused for every entity type, instead of one
 * subclass per entity (CinemaNotFoundException, FilmNotFoundException, ...).
 * This is the generic/"genericite" building block behind AbstractCrudService::get().
 */
class EntityNotFoundException extends DomainException
{
    public static function forId(string $entityClass, int|string $id): self
    {
        $shortName = substr((string) strrchr($entityClass, '\\'), 1) ?: $entityClass;

        return new self(sprintf('%s #%s introuvable.', $shortName, $id));
    }
}
