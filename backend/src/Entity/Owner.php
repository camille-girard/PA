<?php

namespace App\Entity;

use App\Repository\OwnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OwnerRepository::class)]
class Owner extends User
{
    /**
     * @var Collection<int, Accommodation>
     */
    #[ORM\OneToMany(targetEntity: Accommodation::class, mappedBy: 'owner', orphanRemoval: true)]
    #[Groups(['owner:read'])]
    private Collection $accommodations;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'owner')]
    private Collection $messages;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    #[Groups(['owner:read'])]
    private float $notation = 0;

    public function __construct()
    {
        parent::__construct();
        $this->accommodations = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * @return Collection<int, Accommodation>
     */
    #[Groups(['owner:read'])]
    public function getAccommodations(): Collection
    {
        return $this->accommodations;
    }

    public function addAccommodation(Accommodation $accommodation): static
    {
        if (!$this->accommodations->contains($accommodation)) {
            $this->accommodations->add($accommodation);
            $accommodation->setOwner($this);
        }

        return $this;
    }

    public function removeAccommodation(Accommodation $accommodation): static
    {
        if ($this->accommodations->removeElement($accommodation)) {
            // set the owning side to null (unless already changed)
            if ($accommodation->getOwner() === $this) {
                $accommodation->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setOwner($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getOwner() === $this) {
                $message->setOwner(null);
            }
        }

        return $this;
    }

    #[Groups(['owner:read'])]
    public function getAccommodationCount(): int
    {
        return $this->accommodations->count();
    }

    #[Groups(['owner:read'])]
    public function getNotation(): float
    {
        return $this->notation;
    }

    public function setNotation(float $notation): static
    {
        $this->notation = $notation;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }
}
