<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Repository\EpisodeRepository;
use App\Repository\RealSeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface; 

#[Route('/series')]
class SeriesController extends AbstractController
{
    
    #[Route('/', name: 'app_series_index', methods: ['POST','GET'])]
    public function catalogue(ManagerRegistry $doctrine,RealSeriesRepository $repository, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $nb=$repository->findNbSerie();
        $genres = $entityManager
        ->getRepository(Series::class)
        ->getAllGenre();
        /*
        if(isset($_GET['initiale']) || isset($_GET['annee'])){
            $initiale = $_GET['initiale'];
            $annee = $_GET['annee'];
            $seriesCherchees = $entityManager
            ->getRepository(Series::class)
            ->rechercheSansGenre($initiale,$annee);

        
            $em = $doctrine->getManager();
            $repository = $em->getRepository(Series::class);
            $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );
            return $this->render('series/index.html.twig', [
                'series' => $seriesCherchees,
                'nb' => $nb,
                'genre' => $genres,
                'series' => $seriesAAfficher,
        ]);
            
        }
        */
        if(isset($_GET['initiale']) || isset($_GET['annee']) || isset($_GET['genre'])){
            $initiale = $_GET['initiale'];
            $annee = $_GET['annee'];
            $genre = $_GET['genre'];

            if (empty($genre)) {
                $seriesCherchees = $entityManager
                ->getRepository(Series::class)
                ->rechercheSansGenre($initiale,$annee);
            
                $em = $doctrine->getManager();
                $repository = $em->getRepository(Series::class);
                $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
                );
                return $this->render('series/index.html.twig', [
                    'series' => $seriesCherchees,
                    'nb' => $nb,
                    'genre' => $genres,
                    'series' => $seriesAAfficher,
                ]);
            }else{
                $idGenre = $entityManager
            ->getRepository(Series::class)
            ->genreVersId($genre);
            $seriesCherchees = $entityManager
            ->getRepository(Series::class)
            ->rechercheAvecGenre($initiale,$annee,$idGenre);
            $em = $doctrine->getManager();
            $nb=$repository->findNbSerie();
            $repository = $em->getRepository(Series::class);
            $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );
            return $this->render('series/index.html.twig', [
                'series' => $seriesCherchees,
                'nb' => $nb,
                'genre' => $genres,
                'series' => $seriesAAfficher,
            ]); 
            }
            $idGenre = $entityManager
            ->getRepository(Series::class)
            ->genreVersId($genre);
            $seriesCherchees = $entityManager
            ->getRepository(Series::class)
            ->rechercheAvecGenre($initiale,$annee,$idGenre);
            $em = $doctrine->getManager();
            $nb=$repository->findNbSerie();
            $repository = $em->getRepository(Series::class);
            $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );
            return $this->render('series/index.html.twig', [
                'series' => $seriesCherchees,
                'nb' => $nb,
                'genre' => $genres,
                'series' => $seriesAAfficher,
            ]);     
        }
        $series = $entityManager
            ->getRepository(Series::class)
            ->findALl();
        
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $nb=$repository->findNbSerie();
        $dixSeries = [];
        for($i=0;$i<10;$i++){
            
            array_push($dixSeries,$series[rand(0,$nb-1)]);
        }
        $seriesAAfficher = $paginator->paginate(
            $series, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );
        return $this->render('series/index.html.twig', [
            'series' => $seriesAAfficher,
            'nb' => $nb,
            'genre' => $genres
        ]);
    }

    #[Route('/suppr/{id}/{season}', name: 'suppr_serie')]
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
            'user' => $this->getUser(),
            'currentSeason' => $season
        ]);
    }

    #[Route('/suivre/{id}/{season}', name: 'suivre_serie')]
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
            'user' => $this->getUser(),
            'currentSeason' => $season
        ]);
    }

    #[Route('/poster/{id}', name: 'app_series_poster', methods: ['GET'])]
    public function getPoster(Series $series): Response
    {
        return new Response(stream_get_contents($series->getPoster()),200,array('Content-type'=>'image/jpeg'));
    }
    
    #[Route('/{id}/{season}', name: 'app_series_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, EpisodeRepository $repository, Series $series, EntityManagerInterface $entityManager, Season $season): Response
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
            'episode' => $episode,
            'currentSeason' => $season
        ]);
    }

    #[Route('/aVoir/{id}', name: 'aVoir_episode')]
    public function aVoir(ManagerRegistry $doctrine, Episode $episodeEnCours, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->removeEpisode($episodeEnCours);
        $em->flush();
        return $this->render('series/show.html.twig', [
            'series' => $serie,
            'seasons' => $seasons,
            'episode' => $episode,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/vu/{id}', name: 'vu_episode')]
    public function vu(ManagerRegistry $doctrine,Episode $episodeEnCours, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));

        $em = $doctrine->getManager();
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->addEpisode($episodeEnCours);
        $em->flush();
        return $this->render('series/show.html.twig', [
            'series' => $serie,
            'seasons' => $seasons,
            'episode' => $episode,
            'user' => $this->getUser()
        ]);
    }
}
