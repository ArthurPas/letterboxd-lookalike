<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\EpisodeRepository;
use App\Repository\RealSeriesRepository;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'app_series_index', methods: ['POST','GET'])]
    public function index(ManagerRegistry $doctrine,RealSeriesRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $page = 0;
        if(isset($_GET['nb'])){
            $page = $_GET['nb'];
            $page = intval(trim($page,"%2F"));

        }

        if(isset($_GET['initiale']) || isset($_GET['annee'])){
            $initiale = $_GET['initiale'];
            $annee = $_GET['annee'];
            $seriesCherchees = $entityManager
            ->getRepository(Series::class)
            ->rechercheSansGenre($initiale,$annee);

        
            $em = $doctrine->getManager();
            $nb=$repository->findNbSerie();
            $repository = $em->getRepository(Series::class);

            return $this->render('series/index.html.twig', [
                'series' => $seriesCherchees,
                'nb' => $nb
        ]);
            
        }
        if(isset($_GET['initiale']) || isset($_GET['annee']) || isset($_GET['genre'])){
            $initiale = $_GET['initiale'];
            $annee = $_GET['annee'];
            $genre = $_GET['genre'];
            $seriesCherchees = $entityManager
            ->getRepository(Series::class)
            ->rechercheAvecGenre($initiale,$annee,$genre);

        
            $em = $doctrine->getManager();
            $nb=$repository->findNbSerie();
            $repository = $em->getRepository(Series::class);

            return $this->render('series/index.html.twig', [
                'series' => $seriesCherchees,
                'nb' => $nb
        ]);
            
        }
        

        $series = $entityManager
            ->getRepository(Series::class)
            //->findBy([],[], 10, $page*10);
            ->findALl();
        
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $nb=$repository->findNbSerie();
        $dixSeries = [];

        

        for($i=0;$i<10;$i++){
            
            array_push($dixSeries,$series[rand(0,$nb-1)]);
        }
        return $this->render('series/index.html.twig', [
            'series' => $dixSeries,
            'nb' => $nb

        ]);
    }
    #[Route('/poster/{id}', name: 'app_series_poster', methods: ['GET'])]
    public function getPoster(Series $series): Response
    {
        return new Response(stream_get_contents($series->getPoster()),200,array('Content-type'=>'image/jpeg'));
    }
    #[Route('/{id}/{season}', name: 'app_series_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine,EpisodeRepository $repository, Series $series, EntityManagerInterface $entityManager, Season $season): Response
    {
        $render = $entityManager
            ->getRepository(Season::class)
            ->findBy(array('series'=>$series->getId()), array('number'=>'ASC'));

        $em = $doctrine->getManager();
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($series->getId(),$season->getNumber());

        return $this->render('series/show.html.twig', [
            'series' => $series,
            'seasons' => $render,
            'episode' => $episode
        ]);
    }


    
}
