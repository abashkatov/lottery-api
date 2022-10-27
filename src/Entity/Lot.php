<?php

namespace App\Entity;

use App\Repository\LotRepository;
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
}
