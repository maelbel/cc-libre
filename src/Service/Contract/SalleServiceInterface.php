<?php

declare(strict_types=1);

namespace App\Service\Contract;

use App\Entity\Cinema;
use App\Entity\Salle;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends CrudServiceInterface<Salle>
 */
interface SalleServiceInterface extends CrudServiceInterface
{
    public function search(?string $query, ?Cinema $cinema): QueryBuilder;
}
