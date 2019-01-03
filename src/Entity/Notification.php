<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Notyfikacja miała być bytem abstrakcyjnym, niepowiązanym z ORM
 */
/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2000)
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sent_notifications")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $recipient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $context;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     * @Assert\Choice({"read", "unread"})
     */
    private $status = 'unread';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NotificationType")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $meta;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSent(): ?\DateTimeInterface
    {
        return $this->sent;
    }

    public function setSent(\DateTimeInterface $sent): self
    {
        $this->sent = $sent;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getContext(): ?Message
    {
        return $this->context;
    }

    public function setContext(?Message $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?NotificationType
    {
        return $this->type;
    }

    public function setType(?NotificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta($meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setSent(new \DateTime());
    }
}
