<?php

namespace App\Repository;

use App\Entity\Bid;
use App\Entity\Lot;
use App\ValueObject\LotCounterData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lot>
 *
 * @method Lot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lot[]    findAll()
 * @method Lot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lot::class);
    }

    public function save(Lot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function getCounters(int $userId): LotCounterData
    {
        $sql = 'select count(*) as total, count(*) filter (where author_id=:userId) as my from lot;';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery(['userId' => $userId]);
        $data = $result->fetchAssociative();
        return new LotCounterData($data['total'], $data['my']);
    }

    /**
     * @return Lot[] Returns an array of Lot objects
     */
    public function findByOtherUsers(int $userId, array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createSearchLotsQueryBuilder($criteria, $orderBy, $limit, $offset);
        $qb->andWhere('l.authorId != :userId')
            ->setParameter('userId', $userId);
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Lot[] Returns an array of Lot objects
     */
    public function findByMyBet(int $userId, array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createSearchLotsQueryBuilder($criteria, $orderBy, $limit, $offset);
        $qb->innerJoin('l.bids', 'b')
            ->andWhere('b.bidderId = :userId')
            ->setParameter('userId', $userId);
//        die($qb->getQuery()->getSQL());
        return $qb->getQuery()->getResult();
    }

    private function createSearchLotsQueryBuilder(array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('l');
        foreach ($criteria as $key => $value) {
            $qb
                ->andWhere("l.{$key} = :{$key}")
                ->setParameter($key, $value);
        }
        $isOrdered = false;
        foreach ($orderBy as $key => $value) {
            if (is_int($key)) {
                $field = $value;
                $dest = null;
            } else {
                $field = $key;
                $dest = $value;
            }
            $field = 'l.' . $field;
            if ($isOrdered) {
                $qb->addOrderBy($field, $dest);
            } else {
                $isOrdered = true;
                $qb->orderBy($field, $dest);
            }
        }
        if (is_int($limit) && $limit > 0) {
            $qb->setMaxResults($limit);
        }
        if (is_int($offset) && $offset > 0) {
            $qb->setFirstResult($offset);
        }
        return $qb;
    }

    /**
     * @throws Exception
     */
    public function closeLotsBySales(): int
    {
        $sql = <<<SQL
UPDATE lot SET status = :salesStatus WHERE status = :openStatus AND bidding_end <= now() AND last_bidder IS NOT NULL
SQL;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery([
            'salesStatus' => 'sales',
            'openStatus' => 'open',
        ]);
        $result->fetchAllAssociative();
        return $result->rowCount();
    }

    /**
     * @throws Exception
     */
    public function closeLotsByNotSales(): int
    {
        $sql = <<<SQL
UPDATE lot SET status = :closeStatus WHERE status = :openStatus AND bidding_end <= now() AND last_bidder IS NULL
SQL;
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery([
            'closeStatus' => 'closed',
            'openStatus' => 'open',
        ]);
        $result->fetchAllAssociative();
        return $result->rowCount();
    }

//    /**
//     * @return Lot[] Returns an array of Lot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lot
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
