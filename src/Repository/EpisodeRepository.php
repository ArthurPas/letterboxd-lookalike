<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 *
 * @method Episode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Episode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Episode[]    findAll()
 * @method Episode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function findEpisodes($IdSerie, $nb)
    {
        
        return $this->createQueryBuilder('e')
        ->join('e.season', 's')
        ->join('s.series', 'sr')
        ->where('sr.id = :sId')
        ->andWhere('s.number = :sNb')
        ->orderBy('e.number')
        ->setParameters(array('sId'=> $IdSerie,'sNb'=>$nb))
        ->getQuery()
        ->getResult();
        
    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }
}
