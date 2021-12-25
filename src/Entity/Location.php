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
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="locations",cascade={"persist"})
     */
    private $members;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Images", mappedBy="locations",cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=Demande::class, mappedBy="location")
     */
    private $demandes;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        
        $this->images = new ArrayCollection();
        $this->demandes = new ArrayCollection();
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
            $member->setLocations($this);
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

    

    
    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setLocations($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getLocations() === $this) {
                $image->setLocations(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Demande[]
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes[] = $demande;
            $demande->setLocation($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getLocation() === $this) {
                $demande->setLocation(null);
            }
        }

        return $this;
    }
    
}
