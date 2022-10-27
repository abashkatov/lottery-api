<?php

namespace App\Repository;

use App\Entity\Lot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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
     * @return Lot[] Returns an array of Lot objects
     */
    public function findByOtherUsers(int $userId, array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.authorId != :userId')
            ->setParameter('userId', $userId);
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
        return $qb->getQuery()->getResult();
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
