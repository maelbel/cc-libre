<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Seance : projection d'un Film, dans une Salle, sur un creneau Horaire donne.
 *
 * MCD origin: entity "Seance" (id, tarif), * <-> 1..1 with "Salle", "Film" and
 * "Horaire". The MCD attributes alone cannot answer the functional requirement
 * "afficher les films programmes A LA DATE DU JOUR" (Horaire only carries a
 * time-of-day, not a calendar date), so a `date` attribute was added to Seance
 * during the MCD -> MLD translation. This is documented explicitly in the
 * README as a deliberate, justified extension of the model.
 */
#[ORM\Entity(repositoryClass: SeanceRepository::class)]
#[ORM\Table(name: 'seance')]
#[ORM\Index(name: 'idx_seance_date', columns: ['date'])]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull(message: 'La date de la seance est obligatoire.')]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    #[Assert\NotNull(message: 'Le tarif est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le tarif doit etre positif.')]
    private ?string $tarif = null;

    #[ORM\ManyToOne(targetEntity: Salle::class, inversedBy: 'seances')]
    #[ORM\JoinColumn(name: 'salle_id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'La seance doit avoir une salle.')]
    private ?Salle $salle = null;

    #[ORM\ManyToOne(targetEntity: Film::class, inversedBy: 'seances')]
    #[ORM\JoinColumn(name: 'film_id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'La seance doit avoir un film.')]
    private ?Film $film = null;

    #[ORM\ManyToOne(targetEntity: Horaire::class, inversedBy: 'seances', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'horaire_id', nullable: false, onDelete: 'RESTRICT')]
    #[Assert\NotNull(message: 'La seance doit avoir un horaire.')]
    private ?Horaire $horaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTarif(): ?string
    {
        return $this->tarif;
    }

    public function setTarif(string $tarif): static
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): static
    {
        $this->film = $film;

        return $this;
    }

    public function getHoraire(): ?Horaire
    {
        return $this->horaire;
    }

    public function setHoraire(?Horaire $horaire): static
    {
        $this->horaire = $horaire;

        return $this;
    }

    /**
     * Whether this Seance is programmed for today (used by the public "home"
     * space to only surface today's screenings for a chosen cinema).
     */
    public function isAujourdhui(): bool
    {
        return $this->date !== null && $this->date->format('Y-m-d') === (new \DateTimeImmutable())->format('Y-m-d');
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s (%s)',
            $this->film?->getTitre() ?? '',
            $this->date?->format('d/m/Y') ?? '',
            $this->horaire ?? ''
        );
    }
}
