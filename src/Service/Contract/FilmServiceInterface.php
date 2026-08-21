<?php

declare(strict_types=1);

namespace App\Service\Contract;

use App\Entity\Cinema;
use App\Entity\Film;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends CrudServiceInterface<Film>
 */
interface FilmServiceInterface extends CrudServiceInterface
{
    public function search(?string $query): QueryBuilder;

    /**
     * @return Film[]
     */
    public function findProgrammesAujourdhui(Cinema $cinema): array;
}
