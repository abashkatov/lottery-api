<?php

declare(strict_types=1);

namespace App\Module\Bid\MakeBid;

use App\Enum\LotStatus;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    #[Assert\Expression("this.isBidValid()", message: "The bid must exceed the bidding step")]
    private int $bid;

    #[Assert\Range(minMessage: 'Auction finished', min: '+1 second')]
    private \DateTime $biddingEndAt;

    #[Assert\IdenticalTo(LotStatus::OPEN, message: "The lot should be open")]
    private LotStatus $status;

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

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getBiddingEndAt(): \DateTime
    {
        return $this->biddingEndAt;
    }

    public function setBiddingEndAt(\DateTime $biddingEndAt): self
    {
        $this->biddingEndAt = $biddingEndAt;
        return $this;
    }

    public function getStatus(): LotStatus
    {
        return $this->status;
    }

    public function setStatus(LotStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
