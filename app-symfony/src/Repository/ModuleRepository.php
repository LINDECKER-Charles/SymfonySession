<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Module>
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    /**
     * Récupère les modules qui ne sont pas liés à une catégorie donnée ou qui n'ont pas de catégorie.
     *
     * @param int $categoryId ID de la catégorie à exclure.
     * @return array Liste des modules hors de cette catégorie.
     */
    public function findByNotInCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.module_category != :categoryId OR m.module_category IS NULL')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('m.mudleName', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Calcule le total des jours de tous les programmes associés à une session donnée.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités Doctrine.
     * @param int $sessionId L’ID de la session concernée.
     * @return int Somme totale des jours pour la session.
     */
    public function findTtDays(EntityManagerInterface $entityManager, int $sessionId): int
    {
        $qb = $entityManager->createQuery(
            'SELECT SUM(p.nbDay)
            FROM App\Entity\Programme p
            WHERE p.session = :sessionId'
        )->setParameter('sessionId', $sessionId);

        return (int) $qb->getSingleScalarResult();
    }

    //    /**
    //     * @return Module[] Returns an array of Module objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Module
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
