<?php

namespace App\Entity;

use App\Repository\AccommodationImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccommodationImagesRepository::class)]
class AccommodationImages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column]
    private ?bool $isMain = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Accommodation $accommodation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function isMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): static
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getAccommodation(): ?Accommodation
    {
        return $this->accommodation;
    }

    public function setAccommodation(?Accommodation $accommodation): static
    {
        $this->accommodation = $accommodation;

        return $this;
    }
}
