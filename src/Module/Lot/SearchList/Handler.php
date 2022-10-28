<?php

declare(strict_types=1);

namespace App\Module\Lot\SearchList;

use App\Entity\Lot;
use App\Exception\InvalidParamsException;
use App\Repository\LotRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private LotRepository      $repository;
    private ValidatorInterface $validator;
    private Serializer         $serializer;
    private LoggerInterface    $analyticsLogger;

    public function __construct(
        LotRepository $repository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        LoggerInterface $analyticsLogger,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->analyticsLogger = $analyticsLogger;
    }

    /**
     * @return Lot[]
     * @throws ExceptionInterface
     */
    public function handle(Command $command): array
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid search params", $errors);
        }
        $this->analyticsLogger->info('search', [
            'user_id' => $command->getUserId(),
            'condition' => $this->serializer->normalize($command),
        ]);
        $orderBy = [];
        if ($command->getOrder() !== null) {
            $dest = $command->getDest() ?? 'ASC';
            $orderBy[$command->getOrder()] = $dest;
        }
        $criteria = [];
        if ($command->getStatus() !== null) {
            $criteria['status'] = $command->getStatus();
        }
        if ($command->isOnlyBet()) {
            return $this->repository->findByMyBet(
                $command->getUserId(),
                $criteria,
                $orderBy,
                $command->getLimit(),
                $command->getOffset()
            );
        }
        if ($command->getIsMy() === false) {
            return $this->repository->findByOtherUsers(
                $command->getUserId(),
                $criteria,
                $orderBy,
                $command->getLimit(),
                $command->getOffset()
            );
        }
        if ($command->getIsMy() === true) {
            $criteria['authorId'] = $command->getUserId();
        }

        return $this->repository->findBy(
            $criteria,
            $orderBy,
            $command->getLimit(),
            $command->getOffset()
        );
    }
}
