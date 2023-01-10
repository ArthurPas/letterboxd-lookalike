<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Series>
 *
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealSeriesRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }
    public function findNbSerie()
    {
        
        return $this->createQueryBuilder('s')
        ->select('count(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
        
    }
    public function rechercheAvecGenre($initiale, $annee, $genre){
        $query= $this->createQueryBuilder('s')
        ->join('s.genre', 'g')
        ->where('s.title like :title')
        ->andWhere('s.yearStart like :year')
        ->andWhere(':g MEMBER OF s.genre')
        ->setParameters(array('title'=> '%'.$initiale.'%','year' => '%'.$annee.'%','g'=>$genre))
        ->getQuery()
        ->getResult(); 
            return $query;
    }
    public function rechercheSansGenre($initiale, $annee){
        $query= $this->createQueryBuilder('s')
        ->join('s.genre', 'g')
        ->where('s.title like :title')
        ->andWhere('s.yearStart like :year')
        ->setParameters(array('title'=> '%'.$initiale.'%','year' => '%'.$annee.'%'))
        ->getQuery()
        ->getResult(); 
        return $query;
    }
/*
    public function save(Series $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Series $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
*/
//    /**
//     * @return Series[] Returns an array of Series objects
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

//    public function findOneBySomeField($value): ?Series
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
