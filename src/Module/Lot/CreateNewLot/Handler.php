<?php

declare(strict_types=1);

namespace App\Module\Lot\CreateNewLot;

use App\Entity\Lot;
use App\Enum\LotStatus;
use App\Exception\InvalidParamsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Command $command): Lot
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid lot params", $errors);
        }
        $lot = new Lot();
        $lot
            ->setTitle($command->getTitle())
            ->setDescription($command->getDescription())
            ->setAddress($command->getAddress())
            ->setPriceStart($command->getPriceStart())
            ->setPriceStep($command->getPriceStep())
            ->setStatus(LotStatus::OPEN)
            ->setBiddingEnd($command->getBiddingEnd());
        return $lot;
    }
}
