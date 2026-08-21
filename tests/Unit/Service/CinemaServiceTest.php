<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Cinema;
use App\Exception\EntityNotFoundException;
use App\Repository\CinemaRepository;
use App\Service\CinemaService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CinemaServiceTest extends TestCase
{
    public function testGetThrowsEntityNotFoundExceptionWhenMissing(): void
    {
        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->method('find')->with(42)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(Cinema::class)->willReturn($objectRepository);

        $validator = $this->createMock(ValidatorInterface::class);
        $cinemaRepository = $this->createMock(CinemaRepository::class);

        $service = new CinemaService($entityManager, $validator, $cinemaRepository);

        $this->expectException(EntityNotFoundException::class);
        $service->get(42);
    }

    public function testSearchDelegatesToRepository(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $cinemaRepository = $this->createMock(CinemaRepository::class);
        $cinemaRepository->expects($this->once())
            ->method('findForAdminList')
            ->with('Rennes')
            ->willReturn($queryBuilder);

        $service = new CinemaService($entityManager, $validator, $cinemaRepository);

        $this->assertSame($queryBuilder, $service->search('Rennes'));
    }
}
