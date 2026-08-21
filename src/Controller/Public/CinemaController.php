<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Exception\EntityNotFoundException;
use App\Service\Contract\CinemaServiceInterface;
use App\Service\Contract\FilmServiceInterface;
use App\Service\Contract\SeanceServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace public - detail d'un cinema : films programmes a la date du jour et,
 * pour chaque film, les seances programmees.
 */
class CinemaController extends AbstractController
{
    public function __construct(
        private readonly CinemaServiceInterface $cinemaService,
        private readonly FilmServiceInterface $filmService,
        private readonly SeanceServiceInterface $seanceService,
    ) {
    }

    #[Route('/cinemas/{id}', name: 'app_cinema_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        try {
            $cinema = $this->cinemaService->get($id);
        } catch (EntityNotFoundException) {
            throw $this->createNotFoundException('Ce cinema n\'existe pas.');
        }

        $films = $this->filmService->findProgrammesAujourdhui($cinema);

        $seancesParFilm = [];
        foreach ($films as $film) {
            $seancesParFilm[$film->getId()] = $this->seanceService->findAujourdhuiPourCinemaEtFilm($cinema, $film);
        }

        return $this->render('public/cinema/show.html.twig', [
            'cinema' => $cinema,
            'films' => $films,
            'seancesParFilm' => $seancesParFilm,
        ]);
    }
}
