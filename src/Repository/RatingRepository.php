<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rating>
 *
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }
    /**
     * Requete pour obtenir un avis par l'id de l'user
     */
    public function getRatingById($id){
        $query= $this->createQueryBuilder('r')
        ->where(':id = r.user')
        ->setParameters(array('id'=>$id))
        ->getQuery()
        ->getResult();
        return $query;
    }
    /**
     * Requete de recherche de séries en fonctions de la note des avis
     */
    public function rechercheSerieNote($rating) {
        $query = $this->createQueryBuilder('r')
        ->join('r.series', 's')
        ->select('s.id, s.title, s.plot, s.imdb, s.poster, s.director, s.youtubeTrailer, s.awards, s.yearStart, s.yearEnd')
        ->where('r.value = :note')
        ->groupBy('s.id')
        ->setParameters(array('note' => $rating))
        ->getQuery()
        ->getResult();
        return $query;
    }
}
