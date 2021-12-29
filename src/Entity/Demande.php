<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemandeRepository::class)
 */
class Demande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="demandes")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="demandes")
     */
    private $receiver;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $locationId;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="demandes")
     */
    private $location;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAccept;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $ack;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function setLocationId(int $locationId): self
    {
        $this->locationId = $locationId;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getIsAccept(): ?bool
    {
        return $this->isAccept;
    }

    public function setIsAccept(bool $isAccept): self
    {
        $this->isAccept = $isAccept;

        return $this;
    }

    public function getAck(): ?int
    {
        return $this->ack;
    }

    public function setAck(?int $ack): self
    {
        $this->ack = $ack;

        return $this;
    }
}
