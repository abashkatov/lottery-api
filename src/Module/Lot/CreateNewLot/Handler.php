<?php

declare(strict_types=1);

namespace App\Module\Lot\CreateNewLot;

use App\Entity\Lot;

class Handler
{
    public function handle(Command $command): Lot
    {
        $lot = new Lot();
        $lot
            ->setTitle($command->getTitle())
            ->setDescription($command->getDescription())
            ->setAddress($command->getAddress())
            ->setPriceStart($command->getPriceStart())
            ->setPriceStep($command->getPriceStep())
            ->setBiddingEnd($command->getBiddingEnd());
        return $lot;
    }
}
