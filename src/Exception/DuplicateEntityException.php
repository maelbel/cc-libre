<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Raised when a business-level uniqueness rule is violated, e.g. a Salle
 * numero already used within the same Cinema.
 */
class DuplicateEntityException extends DomainException
{
}
