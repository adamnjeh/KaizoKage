<?php

namespace App\Entity;

use App\Repository\GalerieRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Member;
use App\Entity\Figurine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: GalerieRepository::class)]
class Galerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $published = null;
    
    #[ORM\ManyToOne(inversedBy: 'galeries')]
    private ?Member $creator = null;
    
    /**
     * @var Collection<int, Figurine>
     */
    #[ORM\ManyToMany(targetEntity: Figurine::class, inversedBy: 'galeries')]
    private Collection $figurines;
    
        public function __construct()
    {
        $this->figurines = new ArrayCollection();
    }
    
    public function getCreator(): ?Member
    {
        return $this->creator;
    }
    
    public function setCreator(?Member $creator): self
    {
        $this->creator = $creator;
        
        return $this;
    }
    
    /**
     * @return Collection<int, Figurine>
     */
    public function getFigurines(): Collection
    {
        return $this->figurines;
    }
    
    public function addFigurine(Figurine $figurine): self
    {
        if (!$this->figurines->contains($figurine)) {
            $this->figurines->add($figurine);
        }
        
        return $this;
    }
    
    public function removeFigurine(Figurine $figurine): self
    {
        $this->figurines->removeElement($figurine);
        
        return $this;
    }
        
    public function getId(): ?int
    {
        return $this->id;
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

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }
}
