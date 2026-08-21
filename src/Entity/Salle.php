<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Salle de projection, rattachee a un Cinema.
 *
 * MCD origin: entity "Salle" (capacite, dateConstr.) linked to "Cinema" through
 * the "no" relationship, cardinalities 1,1 (Cinema) <-> * (Salle). In the MCD
 * the room number ("no") is the attribute carried by that relationship, i.e.
 * Salle's natural/business key is the pair (cinema, numero) -- enforced here
 * with a unique index -- while a surrogate id remains the physical PK.
 */
#[ORM\Entity(repositoryClass: SalleRepository::class)]
#[ORM\Table(name: 'salle')]
#[ORM\UniqueConstraint(name: 'uniq_salle_cinema_numero', columns: ['cinema_id', 'numero'])]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Le numero de salle est obligatoire.')]
    #[Assert\Positive(message: 'Le numero de salle doit etre positif.')]
    private ?int $numero = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La capacite est obligatoire.')]
    #[Assert\Positive(message: 'La capacite doit etre positive.')]
    private ?int $capacite = null;

    #[ORM\Column(name: 'date_construction', type: 'date_immutable')]
    #[Assert\NotNull(message: 'La date de construction est obligatoire.')]
    private ?\DateTimeImmutable $dateConstruction = null;

    #[ORM\ManyToOne(targetEntity: Cinema::class, inversedBy: 'salles')]
    #[ORM\JoinColumn(name: 'cinema_id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'La salle doit etre rattachee a un cinema.')]
    private ?Cinema $cinema = null;

    /** @var Collection<int, Seance> */
    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'salle', orphanRemoval: true, cascade: ['persist'])]
    private Collection $seances;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getDateConstruction(): ?\DateTimeImmutable
    {
        return $this->dateConstruction;
    }

    public function setDateConstruction(\DateTimeImmutable $dateConstruction): static
    {
        $this->dateConstruction = $dateConstruction;

        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): static
    {
        $this->cinema = $cinema;

        return $this;
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): static
    {
        if (!$this->seances->contains($seance)) {
            $this->seances->add($seance);
            $seance->setSalle($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): static
    {
        if ($this->seances->removeElement($seance)) {
            if ($seance->getSalle() === $this) {
                $seance->setSalle(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Salle %d (%s)', $this->numero ?? 0, $this->cinema?->getNom() ?? '');
    }
}
