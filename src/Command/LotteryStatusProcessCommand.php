<?php

namespace App\Command;

use App\Repository\LotRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:lottery:status-process',
    description: 'Processes all auctions that have expired',
)]
class LotteryStatusProcessCommand extends Command
{
    private LotRepository $lotRepository;

    public function __construct(LotRepository $lotRepository)
    {
        parent::__construct();
        $this->lotRepository = $lotRepository;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sales = $this->lotRepository->closeLotsBySales();
        $time = (new \DateTime())->format('c');
        $io->success("[{$time}] Successfully completed auctions: {$sales}");

        $closed = $this->lotRepository->closeLotsByNotSales();
        $time = (new \DateTime())->format('c');
        $io->success("[{$time}] Completed auctions without a single bid: {$closed}");

        return Command::SUCCESS;
    }
}
