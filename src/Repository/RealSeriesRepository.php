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
        //->join('s.genre', 'g')
        ->where('s.title like :title')
        ->andWhere('s.yearStart like :year')
        ->setParameters(array('title'=> '%'.$initiale.'%','year' => '%'.$annee.'%'))
        ->getQuery()
        ->getResult(); 
        return $query;
    }
    public function getAllGenre(){
        $query= $this->createQueryBuilder('s')
        ->join('s.genre', 'g')
        ->select('g.name')
        ->groupBy('g.name')
        ->getQuery()
        ->getResult(); 
        return $query;
    }
    public function genreVersId($genre){
        $query= $this->createQueryBuilder('s')
        ->join('s.genre', 'g')
        ->select('g.id')
        ->where('g.name = :g')
        ->groupBy('g.id')
        ->setParameters(array('g'=>$genre))
        ->getQuery()
        ->getResult();
        return $query;
    }
    public function seriesSuivies($user)
    {
        $query= $this->createQueryBuilder('s')
        ->where(':u  MEMBER OF s.user')
        ->setParameters(array('u'=>$user))
        ->getQuery()
        ->getResult();
        return $query;
    }
    public function getAllId(){
        $query = $this->createQueryBuilder('s')
        ->select('s.id')
        ->getQuery()
        ->getResult();
        return $query;
    }
    public function findDixSeries($un,$deux,$trois,$quatre,$cinq,$six,$sept,$huit,$neuf,$dix){
        $query=$this->createQueryBuilder('s')
        ->where('s.id = :un or s.id = :deux or s.id = :trois or s.id = :quatre or s.id = :cinq or
        s.id = :s or s.id = :sept or s.id = :huit or s.id = :neuf or s.id = :dix')
        ->setParameters(array('un'=>$un,'deux'=>$deux,'trois'=>$trois, 'quatre'=>$quatre, 'cinq'=>$cinq,
        's'=>$six,'sept'=>$sept,'huit'=>$huit,'neuf'=>$neuf,'dix'=>$dix))
        ->getQuery()
        ->getResult();
        return $query;
    }

    public function rechercheRang($rating) {
        if (!empty($rating)){
            $query = $this->createQueryBuilder('s')
            ->where()
        } 
    }

}


