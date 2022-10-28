<?php

declare(strict_types=1);

namespace App\Module\Bid\MakeBid;

use App\Entity\Lot;
use App\Exception\InvalidParamsException;
use App\Repository\BidRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface $validator;
    private BidRepository      $bidRepository;

    public function __construct(ValidatorInterface $validator, BidRepository $bidRepository)
    {
        $this->validator = $validator;
        $this->bidRepository = $bidRepository;
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public function handle(Lot $lot, Command $command): bool
    {
        $command->setCurrentBid($lot->getCurrentBid());
        $command->setPriceStep($lot->getPriceStep());
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid lot params", $errors);
        }
        return $this->bidRepository->bid($lot, $command);
    }
}
