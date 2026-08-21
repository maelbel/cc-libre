<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Base class for every business-rule exception raised by the service layer.
 * Catching this single type (or its subclasses) lets controllers stay thin:
 * they don't need to know which specific rule was broken to react to it.
 */
abstract class DomainException extends \RuntimeException
{
}
