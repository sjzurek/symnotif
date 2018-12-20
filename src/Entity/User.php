<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $attribute;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="sender", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="recipient")
     */
    private $sent_notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->sent_notifications = new ArrayCollection();
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

    public function getAttribute(): ?int
    {
        return $this->attribute;
    }

    public function setAttribute(int $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setSender($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getSender() === $this) {
                $notification->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getSentNotifications(): Collection
    {
        return $this->sent_notifications;
    }

    public function addSentNotification(Notification $sentNotification): self
    {
        if (!$this->sent_notifications->contains($sentNotification)) {
            $this->sent_notifications[] = $sentNotification;
            $sentNotification->setRecipient($this);
        }

        return $this;
    }

    public function removeSentNotification(Notification $sentNotification): self
    {
        if ($this->sent_notifications->contains($sentNotification)) {
            $this->sent_notifications->removeElement($sentNotification);
            // set the owning side to null (unless already changed)
            if ($sentNotification->getRecipient() === $this) {
                $sentNotification->setRecipient(null);
            }
        }

        return $this;
    }
}
