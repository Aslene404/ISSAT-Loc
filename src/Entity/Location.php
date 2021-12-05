<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="text")
     */
    private $titre;
    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $dispo_date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="locations")
     */
    private $owner;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $capacity;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="location")
     */
    private $members;
    /**
     * @ORM\Column(type="array")
     */
    private $photos;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }


    

    public function getId()
    {
        return $this->id;
    }
    public function getTitre()
    {
        return $this->titre;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setTitre($titre) {
        $this->titre=$titre;
    }
    public function setDescription($description) {
        $this->description=$description;
    }

    public function getDispoDate(): ?\DateTimeInterface
    {
        return $this->dispo_date;
    }

    public function setDispoDate(\DateTimeInterface $dispo_date): self
    {
        $this->dispo_date = $dispo_date;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getPhotos(): ?array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setLocation($this);
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getLocation() === $this) {
                $member->setLocation(null);
            }
        }

        return $this;
    }
    
}
