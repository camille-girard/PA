<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Enum\TicketStatus;
use App\Entity\TicketMessage;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?User $owner = null;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketMessage::class, cascade: ['persist', 'remove'])]
    #[Groups(['ticket:detail'])]
    private Collection $ticketMessages;

    #[ORM\Column(type: 'string', enumType: TicketStatus::class)]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?TicketStatus $status = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['ticket:list', 'ticket:detail'])]
    private ?string $description = null;


    public function __construct()
    {
        $this->ticketMessages = new ArrayCollection();
        $this->status = TicketStatus::OPEN;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, TicketMessage>
     */
    public function getTicketMessages(): Collection
    {
        return $this->ticketMessages;
    }

    public function addTicketMessage(TicketMessage $ticketMessage): static
    {
        if (!$this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages->add($ticketMessage);
            $ticketMessage->setTicket($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): static
    {
        if ($this->ticketMessages->removeElement($ticketMessage)) {
            if ($ticketMessage->getTicket() === $this) {
                $ticketMessage->setTicket(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?TicketStatus
    {
        return $this->status;
    }

    public function setStatus(TicketStatus $status): static
    {
        $this->status = $status;
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

}
