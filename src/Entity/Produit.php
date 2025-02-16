<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $ecopoint = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Recompense>
     */
    #[ORM\OneToMany(targetEntity: Recompense::class, mappedBy: 'categorie')]
    private Collection $recompenses;

    public function __construct()
    {
        $this->recompenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getEcopoint(): ?int
    {
        return $this->ecopoint;
    }

    public function setEcopoint(int $ecopoint): static
    {
        $this->ecopoint = $ecopoint;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Recompense>
     */
    public function getRecompenses(): Collection
    {
        return $this->recompenses;
    }

    public function addRecompense(Recompense $recompense): static
    {
        if (!$this->recompenses->contains($recompense)) {
            $this->recompenses->add($recompense);
            $recompense->setCategorie($this);
        }

        return $this;
    }

    public function removeRecompense(Recompense $recompense): static
    {
        if ($this->recompenses->removeElement($recompense)) {
            // set the owning side to null (unless already changed)
            if ($recompense->getCategorie() === $this) {
                $recompense->setCategorie(null);
            }
        }

        return $this;
    }
}
