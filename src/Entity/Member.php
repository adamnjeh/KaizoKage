<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Vitrine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Galerie;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Member implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
    
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Vitrine::class, orphanRemoval: true)]
    private Collection $vitrines;
    
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Galerie::class, orphanRemoval: true)]
    private Collection $galeries;
        
    public function __construct()
    {
        $this->vitrines = new ArrayCollection();
        $this->galeries = new ArrayCollection();
    }
    
        
    /**
     * @return Collection<int, Vitrine>
     */
    public function getVitrines(): Collection
    {
        return $this->vitrines;
    }
    
    public function addVitrine(Vitrine $vitrine): self
    {
        if (!$this->vitrines->contains($vitrine)) {
            $this->vitrines->add($vitrine);
            $vitrine->setOwner($this);
        }
        
        return $this;
    }
    
    public function removeVitrine(Vitrine $vitrine): self
    {
        if ($this->vitrines->removeElement($vitrine)) {
            if ($vitrine->getOwner() === $this) {
                $vitrine->setOwner(null);
            }
        }
        
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
            $galerie->setCreator($this);
        }
        
        return $this;
    }
    
    public function removeGalerie(Galerie $galerie): self
    {
        if ($this->galeries->removeElement($galerie)) {
            if ($galerie->getCreator() === $this) {
                $galerie->setCreator(null);
            }
        }
        
        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}
