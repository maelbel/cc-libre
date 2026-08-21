<?php

declare(strict_types=1);

namespace App\Service\Contract;

use App\Entity\Cinema;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends CrudServiceInterface<Cinema>
 */
interface CinemaServiceInterface extends CrudServiceInterface
{
    public function search(?string $query): QueryBuilder;
}
