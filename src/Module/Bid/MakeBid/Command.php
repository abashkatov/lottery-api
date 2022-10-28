<?php

declare(strict_types=1);

namespace App\Module\Bid\MakeBid;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    #[Assert\Expression("this.isBidValid()", message: "The bid must exceed the bidding step")]
    private int $bid;

    private int $currentBid;
    private int $priceStep;
    private int $userId;

    public function getCurrentBid(): int
    {
        return $this->currentBid;
    }

    public function setCurrentBid(int $currentBid): self
    {
        $this->currentBid = $currentBid;
        return $this;
    }

    public function getPriceStep(): int
    {
        return $this->priceStep;
    }

    public function setPriceStep(int $priceStep): self
    {
        $this->priceStep = $priceStep;
        return $this;
    }

    public function getBid(): int
    {
        return $this->bid;
    }

    public function setBid(int $bid): self
    {
        $this->bid = $bid;
        return $this;
    }

    public function isBidValid(): bool
    {
        return $this->bid >= $this->currentBid + $this->priceStep;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
