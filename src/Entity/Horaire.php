<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Creneau horaire (heureDebut / heureFin) reutilisable par plusieurs Seances.
 *
 * MCD origin: entity "Horaire" (id, heureDebut, heureFin), 1..1 -> * "Seance".
 */
#[ORM\Entity(repositoryClass: HoraireRepository::class)]
#[ORM\Table(name: 'horaire')]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'heure_debut', type: 'time_immutable')]
    #[Assert\NotNull(message: "L'heure de debut est obligatoire.")]
    private ?\DateTimeImmutable $heureDebut = null;

    #[ORM\Column(name: 'heure_fin', type: 'time_immutable')]
    #[Assert\NotNull(message: "L'heure de fin est obligatoire.")]
    private ?\DateTimeImmutable $heureFin = null;

    /** @var Collection<int, Seance> */
    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'horaire')]
    private Collection $seances;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeureDebut(): ?\DateTimeImmutable
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(\DateTimeImmutable $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeImmutable
    {
        return $this->heureFin;
    }

    public function setHeureFin(\DateTimeImmutable $heureFin): static
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    #[Assert\Callback]
    public function validateOrdreHeures(ExecutionContextInterface $context): void
    {
        if ($this->heureDebut !== null && $this->heureFin !== null && $this->heureFin <= $this->heureDebut) {
            $context->buildViolation("L'heure de fin doit etre posterieure a l'heure de debut.")
                ->atPath('heureFin')
                ->addViolation();
        }
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s',
            $this->heureDebut?->format('H:i') ?? '',
            $this->heureFin?->format('H:i') ?? ''
        );
    }
}
