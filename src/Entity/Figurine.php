<?php

namespace App\Entity;

use App\Repository\FigurineRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Galerie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: FigurineRepository::class)]
class Figurine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $nom = null;
    
    #[ORM\ManyToOne(inversedBy: 'figurines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vitrine $vitrine = null;
    
    /**
     * @var Collection<int, Galerie>
     */
    #[ORM\ManyToMany(targetEntity: Galerie::class, mappedBy: 'figurines')]
    private Collection $galeries;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename = null;
    
    public function __construct()
    {
        $this->galeries = new ArrayCollection();
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
    
    public function getVitrine(): ?Vitrine
    {
        return $this->vitrine;
    }
    
    public function setVitrine(?Vitrine $vitrine): static
    {
        $this->vitrine = $vitrine;
        
        return $this;
    }
    
    /**
     * @return Collection<int, Galerie>
     */
    public function getGaleries(): Collection
    {
        return $this->galeries;
    }
    
    public function addGalerie(Galerie $galerie): self
    {
        if (!$this->galeries->contains($galerie)) {
            $this->galeries->add($galerie);
            // côté inverse ManyToMany
            $galerie->addFigurine($this);
        }
        
        return $this;
    }
    
    public function removeGalerie(Galerie $galerie): self
    {
        if ($this->galeries->removeElement($galerie)) {
            $galerie->removeFigurine($this);
        }
        
        return $this;
    }
    
    /**
     * PROXIES pour Symfony (singularisation "galery")
     * Nécessaires à cause de by_reference=false sur le champ "galeries"
     */
    public function addGalery(Galerie $galerie): self
    {
        return $this->addGalerie($galerie);
    }
    
    public function removeGalery(Galerie $galerie): self
    {
        return $this->removeGalerie($galerie);
    }
    
    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }
    
    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;
        
        return $this;
    }
}
