<?php

declare(strict_types=1);

namespace App\Controller\Public;

use App\Repository\FilmRepository;
use App\Service\Contract\CinemaServiceInterface;
use App\Service\Contract\SeanceServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Espace public - Accueil : liste/recherche des cinemas, recherche de film et
 * affichage des seances disponibles aujourd'hui dans chaque cinema.
 */
class HomeController extends AbstractController
{
    public function __construct(
        private readonly CinemaServiceInterface $cinemaService,
        private readonly SeanceServiceInterface $seanceService,
        private readonly FilmRepository $filmRepository,
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $cinemaSearch = $request->query->get('q');
        $filmSearch = $request->query->get('film');

        $cinemas = $this->cinemaService->search($cinemaSearch)->getQuery()->getResult();

        $seancesAujourdhui = $this->seanceService->findDisponiblesAujourdhui();

        if ($filmSearch !== null && trim($filmSearch) !== '') {
            $seancesAujourdhui = array_values(array_filter(
                $seancesAujourdhui,
                static fn ($seance) => stripos((string) $seance->getFilm()?->getTitre(), trim($filmSearch)) !== false
            ));
        }

        // Group seances by cinema for a clear "seances disponibles dans chaque cinema" display.
        $seancesParCinema = [];
        foreach ($seancesAujourdhui as $seance) {
            $cinemaId = $seance->getSalle()?->getCinema()?->getId();
            $seancesParCinema[$cinemaId][] = $seance;
        }

        return $this->render('public/home/index.html.twig', [
            'cinemas' => $cinemas,
            'cinemaSearch' => $cinemaSearch,
            'filmSearch' => $filmSearch,
            'seancesParCinema' => $seancesParCinema,
        ]);
    }
}
