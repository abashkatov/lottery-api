<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Entity\Lot;

class LotData
{
    private int $id;
    private string $title;
    private string $description;
    private string $address;
    private int $priceStart;
    private int $priceStep;
    private \DateTime $biddingEnd;
    /** @var ImageData[] */
    private array $images;

    /**
     * @param ImageData[] $images
     */
    public function __construct(Lot $lot, array $images) {
        $this->id          = $lot->getId();
        $this->title       = $lot->getTitle();
        $this->description = $lot->getDescription();
        $this->address     = $lot->getAddress();
        $this->priceStart  = $lot->getPriceStart();
        $this->priceStep   = $lot->getPriceStep();
        $this->biddingEnd  = $lot->getBiddingEnd();
        $this->images      = $images;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPriceStart(): int
    {
        return $this->priceStart;
    }

    public function getPriceStep(): int
    {
        return $this->priceStep;
    }

    public function getBiddingEnd(): \DateTime
    {
        return $this->biddingEnd;
    }

    /**
     * @return ImageData[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
