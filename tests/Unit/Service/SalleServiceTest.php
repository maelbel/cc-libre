<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Cinema;
use App\Entity\Salle;
use App\Exception\DuplicateEntityException;
use App\Repository\SalleRepository;
use App\Service\SalleService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Because SalleService depends on interfaces/abstractions (EntityManagerInterface,
 * ValidatorInterface, SalleRepository) rather than concrete Doctrine internals,
 * it can be unit-tested in complete isolation from any database -- the payoff
 * of the Dependency Inversion applied in the service layer.
 */
final class SalleServiceTest extends TestCase
{
    public function testCreateThrowsWhenRoomNumberAlreadyUsedInSameCinema(): void
    {
        $cinema = (new Cinema())->setNom('CineGroupe Test')->setVille('Test')->setRue('Rue Test')->setNumero('1');

        $existingSalle = (new Salle())->setNumero(1)->setCapacite(100)->setDateConstruction(new \DateTimeImmutable('2020-01-01'));
        $existingSalle->setCinema($cinema);

        $newSalle = (new Salle())->setNumero(1)->setCapacite(50)->setDateConstruction(new \DateTimeImmutable('2021-01-01'));
        $newSalle->setCinema($cinema);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $repository = $this->createMock(SalleRepository::class);
        $repository->method('findOneBy')->willReturn($existingSalle);

        $service = new SalleService($entityManager, $validator, $repository);

        $this->expectException(DuplicateEntityException::class);

        $service->create($newSalle);
    }

    public function testCreatePersistsAndFlushesWhenNoConflict(): void
    {
        $cinema = (new Cinema())->setNom('CineGroupe Test')->setVille('Test')->setRue('Rue Test')->setNumero('1');
        $salle = (new Salle())->setNumero(3)->setCapacite(50)->setDateConstruction(new \DateTimeImmutable('2021-01-01'));
        $salle->setCinema($cinema);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist')->with($salle);
        $entityManager->expects($this->once())->method('flush');

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $repository = $this->createMock(SalleRepository::class);
        $repository->method('findOneBy')->willReturn(null);

        $service = new SalleService($entityManager, $validator, $repository);
        $service->create($salle);
    }
}
