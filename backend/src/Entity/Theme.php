<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[UniqueEntity(
    fields: ['name'],
    message: 'Un thème avec ce nom existe déjà.'
)]
#[UniqueEntity(
    fields: ['slug'],
    message: 'Un thème avec ce slug existe déjà.'
)]
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
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ0-9\s\-\'\.]+$/',
        message: "Le nom ne peut contenir que des lettres, chiffres, espaces, tirets, apostrophes et points"
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['theme:read'])]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(
        min: 10,
        max: 2000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    /**
     * @var Collection<int, Accommodation>
     */
    #[ORM\OneToMany(targetEntity: Accommodation::class, mappedBy: 'theme')]
    private Collection $accommodations;

    #[ORM\Column(length: 255)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[Assert\NotBlank(message: "Le slug ne peut pas être vide")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le slug doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le slug ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^[a-z0-9\-]+$/',
        message: "Le slug ne peut contenir que des lettres minuscules, chiffres et tirets"
    )]
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
        $this->name = trim($name);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = trim($description);

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
        $this->slug = trim(strtolower($slug));

        return $this;
    }
}