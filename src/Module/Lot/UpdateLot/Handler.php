<?php

declare(strict_types=1);

namespace App\Module\Lot\UpdateLot;

use App\Entity\Lot;
use App\Exception\InvalidParamsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Lot $lot, Command $command): Lot
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid registration params", $errors);
        }
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
