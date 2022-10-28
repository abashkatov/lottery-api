<?php

declare(strict_types=1);

namespace App\Module\Lot\SearchList;

use App\Entity\Lot;
use App\Exception\InvalidParamsException;
use App\Repository\LotRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private LotRepository      $repository;
    private ValidatorInterface $validator;

    public function __construct(LotRepository $repository, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @return Lot[]
     */
    public function handle(Command $command): array
    {
        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid search params", $errors);
        }
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
