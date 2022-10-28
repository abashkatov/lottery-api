<?php

declare(strict_types=1);

namespace App\Module\Lot\UpdateLot;

use App\Entity\Lot;
use App\Enum\LotStatus;
use App\Exception\InvalidParamsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface  $validator;
    private LoggerInterface     $analyticsLogger;
    private Serializer $serializer;

    public function __construct(
        ValidatorInterface $validator,
        LoggerInterface $analyticsLogger,
        SerializerInterface $serializer,
    ) {
        $this->validator = $validator;
        $this->analyticsLogger = $analyticsLogger;
        $this->serializer = $serializer;
    }

    /**
     * @throws ExceptionInterface
     */
    public function handle(Lot $lot, Command $command): Lot
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid lot params", $errors);
        }
        $lot
            ->setTitle($command->getTitle())
            ->setDescription($command->getDescription())
            ->setAddress($command->getAddress())
            ->setPriceStart($command->getPriceStart())
            ->setPriceStep($command->getPriceStep())
            ->setBiddingEnd($command->getBiddingEnd());
        if ($command->getStatus() !== null) {
            $lot->setStatus(LotStatus::from($command->getStatus()));
        }
        $this->analyticsLogger->info('update lot', [
            'user_id' => $lot->getAuthorId(),
            'patch' => $this->serializer->normalize($command),
        ]);
        return $lot;
    }
}
