<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['conversation:read', 'conversation:list'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['conversation:read', 'conversation:list'])]
    private ?Client $client = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['conversation:read', 'conversation:list'])]
    private ?Owner $owner = null;

    #[ORM\Column]
    #[Groups(['conversation:read', 'conversation:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['conversation:read', 'conversation:list'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, orphanRemoval: true)]
    #[Groups(['conversation:read'])]
    private Collection $messages;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['conversation:read', 'conversation:list'])]
    private ?string $lastMessagePreview = null;

    #[ORM\Column]
    #[Groups(['conversation:read', 'conversation:list'])]
    private bool $hasNewMessages = false;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function getLastMessagePreview(): ?string
    {
        return $this->lastMessagePreview;
    }

    public function setLastMessagePreview(?string $lastMessagePreview): static
    {
        $this->lastMessagePreview = $lastMessagePreview;

        return $this;
    }

    public function isHasNewMessages(): bool
    {
        return $this->hasNewMessages;
    }

    public function setHasNewMessages(bool $hasNewMessages): static
    {
        $this->hasNewMessages = $hasNewMessages;

        return $this;
    }
}
