<?php

namespace App\Entity;

use App\Enum\AccommodationAdvantage;
use App\Repository\AccommodationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: AccommodationRepository::class)]
class Accommodation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['accommodation:read', 'accommodation:summary', 'booking:read'])]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accommodation:read', 'accommodation:summary', 'booking:read', 'owner:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?string $address = null;

    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $type = null;

    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\Column(nullable: true)]
    private ?int $bedrooms = null;

    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\Column(nullable: true)]
    private ?int $bathrooms = null;

    #[ORM\Column]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?int $capacity = null;

    #[ORM\Column]
    #[Groups(['accommodation:read', 'accommodation:summary', 'booking:read', 'owner:read'])]
    private ?float $price = null;

    /**
     * @var array<AccommodationAdvantage>
     */
    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private array $advantage = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?string $practicalInformations = null;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    #[Groups(['accommodation:read', 'accommodation:summary', 'booking:read', 'owner:read'])]
    private float $rating = 0;

    #[ORM\Column]
    #[Groups(['owner:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'accommodation')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[MaxDepth(1)]
    private ?Owner $owner = null;

    #[ORM\ManyToOne(inversedBy: 'accommodation')]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[MaxDepth(1)]
    private ?Theme $theme = null;

    /**
     * @var Collection<int, AccommodationImages>
     */
    #[ORM\OneToMany(mappedBy: 'accommodation', targetEntity: AccommodationImages::class)]
    #[Groups(['accommodation:read', 'accommodation:summary', 'booking:read', 'owner:read'])]
    private Collection $images;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(mappedBy: 'accommodation', targetEntity: Booking::class, orphanRemoval: true)]
    #[Groups(['owner:read'])]
    private Collection $bookings;

    /**
     * @var Collection<int, Comment>
     */
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    #[ORM\OneToMany(mappedBy: 'accommodation', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[Groups(['booking:read', 'owner:read', 'accommodation:read'])]
    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[Groups(['booking:read', 'owner:read', 'accommodation:read'])]
    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?int $minStay = 1;

    #[ORM\Column(type: 'integer', options: ['default' => 7])]
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    private ?int $maxStay = 7;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
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

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(?int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(?int $bathrooms): static
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return array<AccommodationAdvantage>
     */
    public function getAdvantage(): array
    {
        return $this->advantage;
    }

    /**
     * @param array<AccommodationAdvantage> $advantage
     */
    public function setAdvantage(array $advantage): static
    {
        $this->advantage = $advantage;

        return $this;
    }

    public function getPracticalInformations(): ?string
    {
        return $this->practicalInformations;
    }

    public function setPracticalInformations(?string $practicalInformations): static
    {
        $this->practicalInformations = $practicalInformations;

        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection<int, AccommodationImages>
     */
    #[Groups(['accommodation:read', 'booking:read', 'owner:read'])]
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(AccommodationImages $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAccommodation($this);
        }

        return $this;
    }

    public function removeImage(AccommodationImages $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getAccommodation() === $this) {
                $image->setAccommodation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAccommodation($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getAccommodation() === $this) {
                $booking->setAccommodation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAccommodation($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getAccommodation() === $this) {
                $comment->setAccommodation(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getMinStay(): ?int
    {
        return $this->minStay;
    }

    public function setMinStay(?int $minStay): static
    {
        $this->minStay = $minStay;

        return $this;
    }

    public function getMaxStay(): ?int
    {
        return $this->maxStay;
    }

    public function setMaxStay(?int $maxStay): static
    {
        $this->maxStay = $maxStay;

        return $this;
    }
}
