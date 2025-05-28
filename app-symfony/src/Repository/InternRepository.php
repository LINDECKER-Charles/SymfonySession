<?php

namespace App\Repository;

use App\Entity\Intern;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intern>
 */
class InternRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intern::class);
    }

    public function findByNotInSession(int $sessionId): array
    {
        /* Création de la requet principale */
        $qb = $this->createQueryBuilder('i');

        /* Création de la sous requet */
        $sub = $this->createQueryBuilder('i2')
            ->select('i2.id') /* Récupère tout les id des interns */
            ->innerJoin('i2.sessions', 's') /* Join à la table session */
            ->where('s.id = :sessionId'); /* Ne garde que les id des stagiaire présent dans la session */


        return $qb
            ->where($qb->expr()->notIn('i.id', $sub->getDQL())) /* Ne garde que les intern dont l'id n'est pas dans la sous requet */
            ->setParameter('sessionId', $sessionId) /* Défini la variable en paramètre dans notre sous requet */
            ->getQuery() /* Execute la requet */
            ->getResult(); /* Retourne le résultat */
    }

    //    /**
    //     * @return Intern[] Returns an array of Intern objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Intern
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
