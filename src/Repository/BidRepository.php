<?php

namespace App\Repository;

use App\Entity\Bid;
use App\Entity\Lot;
use App\Module\Bid\MakeBid\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bid>
 *
 * @method Bid|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bid|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bid[]    findAll()
 * @method Bid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bid::class);
    }

    public function save(Bid $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bid $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public function bid(Lot $lot, Command $command): bool
    {
        $this->getEntityManager()->beginTransaction();
        try {
            $sql    = <<<SQL
UPDATE lot SET current_bid = :newBid, last_bidder = :userId 
           WHERE id = :lotId 
             AND current_bid + price_step <= :newBid 
             AND status = 'open'
SQL;
            $stmt   = $this->getEntityManager()->getConnection()->prepare($sql);
            $result = $stmt->executeQuery([
                'lotId'  => $lot->getId(),
                'newBid' => $command->getBid(),
                'userId' => $command->getUserId(),
            ]);
            $data   = $result->fetchAllAssociative();
            if (empty($data)) {
                $this->getEntityManager()->rollback();
                return false;
            }
            $bid = new Bid();
            $bid->setLot($lot)
                ->setBid($command->getBid())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setBidderId($command->getUserId());
            $this->getEntityManager()->persist($bid);
            $this->getEntityManager()->flush();
        } catch (\Throwable $e) {
            $this->getEntityManager()->rollback();
            throw $e;
        }
        $this->getEntityManager()->commit();
        return true;
    }

//    /**
//     * @return Bid[] Returns an array of Bid objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bid
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
