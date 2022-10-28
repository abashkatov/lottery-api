<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Lot;
use App\Enum\LotStatus;
use Doctrine\ORM\EntityManagerInterface;

class LotStatusService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function updateByDateTime(Lot $lot): void
    {
        if ($lot->getStatus() !== LotStatus::OPEN) {
            return;
        }
        if ($lot->getBiddingEnd() > new \DateTime()) {
            return;
        }
        $newStatus = $lot->getLastBidder() !== null
            ? LotStatus::SALES
            : LotStatus::CLOSED;
        $lot->setStatus($newStatus);
        $this->em->flush();
    }
}
