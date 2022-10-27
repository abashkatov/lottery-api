<?php

namespace App\Entity;

use App\Repository\LotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotRepository::class)]
class Lot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private ?int $priceStart = null;

    #[ORM\Column]
    private ?int $priceStep = null;

    #[ORM\Column]
    private ?\DateTime $biddingEnd = null;

    #[ORM\OneToMany(mappedBy: 'lot', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(nullable: true)]
    private ?int $authorId = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPriceStart(): ?int
    {
        return $this->priceStart;
    }

    public function setPriceStart(?int $priceStart): self
    {
        $this->priceStart = $priceStart;
        return $this;
    }

    public function getPriceStep(): ?int
    {
        return $this->priceStep;
    }

    public function setPriceStep(?int $priceStep): self
    {
        $this->priceStep = $priceStep;
        return $this;
    }

    public function getBiddingEnd(): ?\DateTime
    {
        return $this->biddingEnd;
    }

    public function setBiddingEnd(?\DateTime $biddingEnd): self
    {
        $this->biddingEnd = $biddingEnd;
        return $this;
   }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setLot($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getLot() === $this) {
                $image->setLot(null);
            }
        }

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthorId(?int $authorId): self
    {
        $this->authorId = $authorId;
        return $this;
    }
}
