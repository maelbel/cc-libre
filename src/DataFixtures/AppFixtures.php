<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Horaire;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Dev/demo data: one admin account (admin@cinegroupe.local / ChangeMe123!)
 * plus a couple of cinemas/salles/films/seances so the public homepage and
 * the admin CRUD pages have something to show right after installation.
 */
class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@cinegroupe.local');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'ChangeMe123!'));
        $manager->persist($admin);

        $cinemaRennes = (new Cinema())->setNom('CineGroupe Rennes Centre')->setVille('Rennes')->setRue('Rue de la Paix')->setNumero('12');
        $cinemaNantes = (new Cinema())->setNom('CineGroupe Nantes Atlantis')->setVille('Nantes')->setRue('Avenue de la Mer')->setNumero('5');
        $manager->persist($cinemaRennes);
        $manager->persist($cinemaNantes);

        $salle1 = (new Salle())->setNumero(1)->setCapacite(150)->setDateConstruction(new \DateTimeImmutable('2010-05-01'));
        $cinemaRennes->addSalle($salle1);
        $salle2 = (new Salle())->setNumero(2)->setCapacite(90)->setDateConstruction(new \DateTimeImmutable('2012-05-01'));
        $cinemaRennes->addSalle($salle2);
        $salle3 = (new Salle())->setNumero(1)->setCapacite(200)->setDateConstruction(new \DateTimeImmutable('2018-01-15'));
        $cinemaNantes->addSalle($salle3);
        $manager->persist($salle1);
        $manager->persist($salle2);
        $manager->persist($salle3);

        $film1 = (new Film())->setTitre('Les Aventuriers de demain')->setAnnee(2024)->setGenre('Aventure');
        $film2 = (new Film())->setTitre('Rire aux eclats')->setAnnee(2023)->setGenre('Comedie');
        $manager->persist($film1);
        $manager->persist($film2);

        $extraFilms = [
            ['Le Fabuleux Destin d\'Amelie Poulain', 2001, 'Comedie'],
            ['Intouchables', 2011, 'Comedie'],
            ['La Haine', 1995, 'Drame'],
            ['Le Diner de cons', 1998, 'Comedie'],
            ['Inception', 2010, 'Science-fiction'],
            ['Interstellar', 2014, 'Science-fiction'],
            ['The Dark Knight', 2008, 'Action'],
            ['Pulp Fiction', 1994, 'Policier'],
            ['Le Seigneur des anneaux : La Communaute de l\'anneau', 2001, 'Fantastique'],
            ['Parasite', 2019, 'Thriller'],
            ['Le Voyage de Chihiro', 2001, 'Animation'],
            ['Mad Max: Fury Road', 2015, 'Action'],
            ['La La Land', 2016, 'Comedie musicale'],
            ['Whiplash', 2014, 'Drame'],
            ['Les Visiteurs', 1993, 'Comedie'],
            ['Le Grand Bleu', 1988, 'Drame'],
            ['Leon', 1994, 'Thriller'],
            ['Matrix', 1999, 'Science-fiction'],
            ['Gladiator', 2000, 'Historique'],
            ['Titanic', 1997, 'Romance'],
            ['Jurassic Park', 1993, 'Aventure'],
            ['Retour vers le futur', 1985, 'Science-fiction'],
            ['Le Roi Lion', 1994, 'Animation'],
            ['Toy Story', 1995, 'Animation'],
            ['Alien', 1979, 'Horreur'],
            ['Shining', 1980, 'Horreur'],
            ['Get Out', 2017, 'Horreur'],
            ['Dune', 2021, 'Science-fiction'],
            ['Oppenheimer', 2023, 'Historique'],
            ['Anatomie d\'une chute', 2023, 'Drame'],
            ['Les Choristes', 2004, 'Drame'],
            ['Bienvenue chez les Ch\'tis', 2008, 'Comedie'],
            ['OSS 117 : Le Caire, nid d\'espions', 2006, 'Comedie'],
            ['Le Bureau des legendes : le film', 2025, 'Espionnage'],
            ['Les Intouchables du rail', 2024, 'Action'],
            ['Ombres sur la Loire', 2025, 'Policier'],
            ['Le Dernier Phare', 2024, 'Drame'],
            ['Nuit blanche a Brest', 2025, 'Thriller'],
            ['Les Gardiens de la foret', 2023, 'Aventure'],
            ['Cosmos Express', 2024, 'Science-fiction'],
            ['Un ete a Quiberon', 2025, 'Romance'],
            ['La Revanche des crepes', 2024, 'Comedie'],
            ['Le Secret du Mont', 2023, 'Fantastique'],
            ['Brume', 2025, 'Horreur'],
            ['Petits Pas', 2024, 'Animation'],
        ];

        foreach ($extraFilms as [$titre, $annee, $genre]) {
            $manager->persist((new Film())->setTitre($titre)->setAnnee($annee)->setGenre($genre));
        }

        $horaire1 = (new Horaire())->setHeureDebut(new \DateTimeImmutable('14:00'))->setHeureFin(new \DateTimeImmutable('16:00'));
        $horaire2 = (new Horaire())->setHeureDebut(new \DateTimeImmutable('20:30'))->setHeureFin(new \DateTimeImmutable('22:30'));
        $manager->persist($horaire1);
        $manager->persist($horaire2);

        $today = new \DateTimeImmutable('today');

        $seance1 = (new Seance())->setDate($today)->setTarif('9.50')->setSalle($salle1)->setFilm($film1)->setHoraire($horaire1);
        $seance2 = (new Seance())->setDate($today)->setTarif('11.00')->setSalle($salle2)->setFilm($film2)->setHoraire($horaire2);
        $seance3 = (new Seance())->setDate($today)->setTarif('10.00')->setSalle($salle3)->setFilm($film1)->setHoraire($horaire2);
        $manager->persist($seance1);
        $manager->persist($seance2);
        $manager->persist($seance3);

        $manager->flush();
    }
}
