<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $name;

    #[ORM\Column(type: 'string', length: 20)]
    private $color;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Magazine::class, orphanRemoval: true)]
    private $magazines;

    public function __construct()
    {
        $this->magazines = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Magazine>
     */
    public function getMagazines(): Collection
    {
        return $this->magazines;
    }

    public function addMagazine(Magazine $magazine): self
    {
        if (!$this->magazines->contains($magazine)) {
            $this->magazines[] = $magazine;
            $magazine->setCategory($this);
        }

        return $this;
    }

    public function removeMagazine(Magazine $magazine): self
    {
        if ($this->magazines->removeElement($magazine)) {
            // set the owning side to null (unless already changed)
            if ($magazine->getCategory() === $this) {
                $magazine->setCategory(null);
            }
        }

        return $this;
    }
    
}
