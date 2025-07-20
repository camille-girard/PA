<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'user' => User::class,
    'client' => Client::class,
    'owner' => Owner::class,
    'admin' => Admin::class,
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read', 'owner:read', 'owner:summary', 'admin:read', 'me:read', 'message:read', 'conversation:read', 'conversation:list'])]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['client:read', 'owner:read', 'owner:summary', 'admin:read', 'me:read', 'message:read', 'conversation:read', 'conversation:list'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['me:read', 'admin:read', 'message:read', 'conversation:read', 'client:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['me:read'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'owner:read', 'owner:summary', 'admin:read', 'booking:read', 'accommodation:read', 'me:read', 'ticket:list', 'ticket:detail', 'message:read', 'conversation:read', 'conversation:list'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'owner:read', 'owner:summary', 'admin:read', 'booking:read', 'accommodation:read', 'me:read', 'ticket:list', 'ticket:detail', 'message:read', 'conversation:read', 'conversation:list'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client:read', 'owner:read', 'owner:summary', 'admin:read', 'me:read'])]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['me:read', 'owner:read', 'admin:read', 'client:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['me:read', 'owner:read', 'admin:read', 'client:read'])]
    private ?string $avatar = null;

    #[ORM\Column]
    #[Groups(['client:read', 'owner:read', 'me:read', 'admin:read'])]
    private ?bool $isVerified = null;

    #[ORM\Column]
    #[Groups(['client:read', 'owner:read', 'me:read', 'admin:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Ticket::class)]
    /**
     * @phpstan-ignore-next-line
     */
    private Collection $tickets;

    #[ORM\Column(type: 'boolean')]
    private bool $isDeleted = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totpSecret = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['client:read', 'owner:read', 'me:read', 'admin:read'])]
    private bool $isTwoFactorEnabled = false;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(targetEntity: TicketMessage::class, mappedBy: 'author')]
    private Collection $ticketMessages;

    public function __construct()
    {
        $this->ticketMessages = new ArrayCollection();
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_CLIENT';

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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
            $ticketMessage->setAuthor($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): static
    {
        if ($this->ticketMessages->removeElement($ticketMessage)) {
            if ($ticketMessage->getAuthor() === $this) {
                $ticketMessage->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setOwner($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getOwner() === $this) {
                $ticket->setOwner(null);
            }
        }

        return $this;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): static
    {
        $this->totpSecret = $totpSecret;

        return $this;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->isTwoFactorEnabled;
    }

    public function setIsTwoFactorEnabled(bool $isTwoFactorEnabled): static
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;

        return $this;
    }
}
