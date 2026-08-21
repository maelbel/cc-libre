<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Raised by AbstractCrudService when Symfony's Validator rejects an entity
 * before it is persisted, carrying the violation list so the controller can
 * turn it into form errors or a flash message.
 */
class ValidationFailedException extends DomainException
{
    public function __construct(private readonly ConstraintViolationListInterface $violations)
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = (string) $violation->getMessage();
        }

        parent::__construct(implode(' ', $messages) ?: 'Donnees invalides.');
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
