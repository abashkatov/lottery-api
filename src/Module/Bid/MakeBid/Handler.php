<?php

declare(strict_types=1);

namespace App\Module\Bid\MakeBid;

use App\Entity\Lot;
use App\Exception\InvalidParamsException;
use App\Repository\BidRepository;
use App\Service\LotStatusService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface $validator;
    private BidRepository      $bidRepository;
    private LotStatusService   $lotStatusService;
    private LoggerInterface    $analyticsLogger;

    public function __construct(
        ValidatorInterface $validator,
        BidRepository $bidRepository,
        LotStatusService $lotStatusService,
        LoggerInterface $analyticsLogger,
    ) {
        $this->validator = $validator;
        $this->bidRepository = $bidRepository;
        $this->lotStatusService = $lotStatusService;
        $this->analyticsLogger = $analyticsLogger;
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public function handle(Lot $lot, Command $command): bool
    {
        $command->setCurrentBid($lot->getCurrentBid());
        $command->setPriceStep($lot->getPriceStep());
        $command->setBiddingEndAt($lot->getBiddingEnd());
        $this->lotStatusService->updateByDateTime($lot);
        $command->setStatus($lot->getStatus());
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid lot params", $errors);
        }
        $this->analyticsLogger->info('bid', [
            'user_id' => $command->getUserId(),
            'bid' => $command->getBid(),
            'lot' => [
                'id' => $lot->getId(),
                'title' => $lot->getTitle(),
            ],
        ]);
        return $this->bidRepository->bid($lot, $command);
    }
}
