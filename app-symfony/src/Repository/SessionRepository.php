<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findSessionsByNamePrefix(string $param): array
    {
        return $this->createQueryBuilder('s')
            ->where('LOWER(s.sessionName) LIKE LOWER(:param)')
            ->setParameter('param', $param . '%') // Recherche des noms qui commencent par $param
            ->getQuery()
            ->getResult();
    }

    public function findSessionsByNameOrderMax(string $ordre, string $search, int $max): array
    {
        return $this->createQueryBuilder('s')
                ->where('LOWER(s.sessionName) LIKE :search')
                ->orderBy('s.sessionName', $ordre)
                ->setParameter('search', $search . '%')
                ->setMaxResults($max)
                ->getQuery()
                ->getResult();
    }
    /*  */

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
