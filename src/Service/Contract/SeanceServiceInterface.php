<?php

declare(strict_types=1);

namespace App\Service\Contract;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Seance;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends CrudServiceInterface<Seance>
 */
interface SeanceServiceInterface extends CrudServiceInterface
{
    public function search(?string $query): QueryBuilder;

    /**
     * @return Seance[]
     */
    public function findAujourdhuiPourCinemaEtFilm(Cinema $cinema, Film $film): array;

    /**
     * @return Seance[]
     */
    public function findDisponiblesAujourdhui(): array;
}
