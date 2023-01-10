<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Genre;
use App\Entity\Season;
use App\Entity\Series;
use App\Repository\EpisodeRepository;
use App\Repository\RealSeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/series')]
class SeriesController extends AbstractController
{
    
    #[Route('/', name: 'app_series_index', methods: ['POST','GET'])]
    public function catalogue(ManagerRegistry $doctrine,RealSeriesRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $page = 0;
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $nb=$repository->findNbSerie();
        if(isset($_GET['nb'])){
            $page = $_GET['nb'];
            $page = intval(trim($page,"%2F"));
            $page = $page % $nb;

        }
        $series = $entityManager
            ->getRepository(Series::class)
            ->findBy([],[], 10, $page*10);
        return $this->render('series/index.html.twig', [
            'series' => $series,
            'nb' => $nb
        ]);
    }

    #[Route('/suppr/{id}', name: 'suppr_serie')]
    public function suivre(ManagerRegistry $doctrine, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));

        $em = $doctrine->getManager();
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->removeSeries($serie);
        $em->flush();
        return $this->render('series/show.html.twig', [
            'series' => $serie,
            'seasons' => $seasons,
            'episode' => $episode,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/suivre/{id}', name: 'suivre_serie')]
    public function suppr(ManagerRegistry $doctrine, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));

        $em = $doctrine->getManager();
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->addSeries($serie);
        $em->flush();
        return $this->render('series/show.html.twig', [
            'series' => $serie,
            'seasons' => $seasons,
            'episode' => $episode,
            'user' => $this->getUser()
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
        $seasons = $entityManager
            ->getRepository(Season::class)
            ->findBy(array('series'=>$series->getId()), array('number'=>'ASC'));

        $em = $doctrine->getManager();
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($series->getId(), $season->getId());

        return $this->render('series/show.html.twig', [
            'series' => $series,
            'seasons' => $seasons,
            'episode' => $episode
        ]);
    }
    
    
}
