<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read', 'theme:read'])]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read', 'theme:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['theme:read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Accommodation>
     */
    #[ORM\OneToMany(targetEntity: Accommodation::class, mappedBy: 'theme')]
    private Collection $accommodations;

    #[ORM\Column(length: 255)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?string $slug = null;

    public function __construct()
    {
        $this->accommodations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Accommodation>
     */
    public function getAccommodations(): Collection
    {
        return $this->accommodations;
    }

    public function addAccommodation(Accommodation $accommodation): static
    {
        if (!$this->accommodations->contains($accommodation)) {
            $this->accommodations->add($accommodation);
            $accommodation->setTheme($this);
        }

        return $this;
    }

    public function removeAccommodation(Accommodation $accommodation): static
    {
        if ($this->accommodations->removeElement($accommodation)) {
            if ($accommodation->getTheme() === $this) {
                $accommodation->setTheme(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
