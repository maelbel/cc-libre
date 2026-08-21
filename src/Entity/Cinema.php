<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CinemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cinema (salle de cinema / etablissement).
 *
 * MCD origin: entity "Cinema" (nom, ville, rue, numero) related 1..1 -> * to "Salle"
 * via the "no" relationship. A technical surrogate id is used as the physical
 * primary key (rather than the natural key "nom") so that foreign keys and
 * URLs stay short and stable; "nom" remains a unique business key.
 */
#[ORM\Entity(repositoryClass: CinemaRepository::class)]
#[ORM\Table(name: 'cinema')]
#[ORM\HasLifecycleCallbacks]
class Cinema
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Assert\NotBlank(message: 'Le nom du cinema est obligatoire.')]
    #[Assert\Length(max: 150)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La ville est obligatoire.')]
    #[Assert\Length(max: 100)]
    private ?string $ville = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'La rue est obligatoire.')]
    #[Assert\Length(max: 150)]
    private ?string $rue = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le numero de voie est obligatoire.')]
    #[Assert\Length(max: 20)]
    private ?string $numero = null;

    /** @var Collection<int, Salle> */
    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'cinema', orphanRemoval: true, cascade: ['persist'])]
    private Collection $salles;

    public function __construct()
    {
        $this->salles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): static
    {
        $this->rue = $rue;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Full postal address, built from rue + numero + ville (Tell-Don't-Ask:
     * callers ask the Cinema to format itself instead of poking at fields).
     */
    public function getAdresseComplete(): string
    {
        return sprintf('%s %s, %s', $this->numero, $this->rue, $this->ville);
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): static
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->setCinema($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            if ($salle->getCinema() === $this) {
                $salle->setCinema(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}
