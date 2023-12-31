<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Entity\Rating;
use App\Entity\User;
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
    /**
     * Permet de faire un tableau qui compte combien il y a de notes en fonction de la valeur de la note
     */
    public function totaux($ratings) {
        $tabTotaux = array_fill(0, 11, 0);

        foreach ($ratings as $rating) {
            $tabTotaux[$rating->getValue() * 2] += 1;
        }

        return array_reverse($tabTotaux);
    }
    /**
     * Compte le nombre de follower d'une série
     */
    public function compterSuiveur($user) {
        return count($user);
    }
    /**
     * Sommes des notes
     */
    public function totalNotes($ratings) {
        $somme = 0;

        foreach ($ratings as $rating) {
            $somme += $rating->getValue();
        }

        return $somme;
    }
    
    #[Route('/', name: 'app_series_index', methods: ['POST','GET'])]
    public function catalogue(ManagerRegistry $doctrine,RealSeriesRepository $repository, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $genres = $entityManager
        ->getRepository(Series::class)
        ->getAllGenre();
        $seriesSuiviesRecupere = $repository->seriesSuivies($this->getUser());
            $seriesSuivies = $paginator->paginate(
            $seriesSuiviesRecupere, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
            );
        if(isset($_GET['initiale']) || isset($_GET['annee']) || isset($_GET['genre']) || isset($_GET['rating'])){
            $initiale = $_GET['initiale'];
            $annee = $_GET['annee'];
            $genre = $_GET['genre'];
            $rating = $_GET['rating'];

            if (empty($genre)) {
                $seriesCherchees = $entityManager
                ->getRepository(Series::class)
                ->rechercheSansGenre($initiale,$annee);
                if(!empty($rating)){
                    $seriesCherchees = $entityManager
                    ->getRepository(Rating::class)
                    ->rechercheSerieNote($rating);
                }
                $repository = $em->getRepository(Series::class);
                $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
                );
                return $this->render('series/index.html.twig', [
                    'series' => $seriesCherchees,
                    'genre' => $genres,
                    'series' => $seriesAAfficher,
                    'seriesSuivies' => $seriesSuivies,
                    'rating' => $rating,
                ]);
            }else{
                $idGenre = $entityManager
                ->getRepository(Series::class)
                ->genreVersId($genre);
                $seriesCherchees = $entityManager
                ->getRepository(Series::class)
                ->rechercheAvecGenre($initiale,$annee,$idGenre);
                if(!empty($rating)){
                    $seriesCherchees = $entityManager
                    ->getRepository(Rating::class)
                    ->rechercheSerieNote($rating);
                }
                $repository = $em->getRepository(Series::class);
                $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
                );

                return $this->render('series/index.html.twig', [
                    'series' => $seriesCherchees,
                    'genre' => $genres,
                    'series' => $seriesAAfficher,
                    'seriesSuivies' => $seriesSuivies,
                ]); 
            }

            $idGenre = $entityManager
            ->getRepository(Series::class)
            ->genreVersId($genre);
            $seriesCherchees = $entityManager
            ->getRepository(Series::class)
            ->rechercheAvecGenre($initiale,$annee,$idGenre);
            //$em = $doctrine->getManager();
            $repository = $em->getRepository(Series::class);
            $seriesAAfficher = $paginator->paginate(
                $seriesCherchees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );
           
            return $this->render('series/index.html.twig', [
                'series' => $seriesCherchees,
                'genre' => $genres,
                'series' => $seriesAAfficher,
                'seriesSuivies' => $seriesSuivies,
                // last username entered by the usersSuivies,
            ]);     
        }

        $series = $entityManager
            ->getRepository(Series::class)
            ->findALl();
        
        //$em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $seriesAAfficher = $paginator->paginate(
            $series, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );
        
        return $this->render('series/index.html.twig', [
            'series' => $seriesAAfficher,
            'genre' => $genres,
            'seriesSuivies' => $seriesSuivies
        ]);
    }

    #[Route('/edit/{seriesId}', name: 'app_series_edit')]
    public function editerSerie(ManagerRegistry $doctrine, EntityManagerInterface $em, Series $seriesId)
    {
        if ($this->getUser()->isAdmin()) {
            $em = $doctrine->getManager();
            $repository = $em->getRepository(Series::class);

            $serie = $repository->findOneBy(['id' => $seriesId]);
            
            
            return $this->render('series/edit.html.twig', [
                'serie' => $serie,


            ]);
        }
        else {
            return $this->redirectToRoute('app_series_index');
        }
    }

    #[Route('/suppr/{id}/{season}', name: 'suppr_serie')]
    public function suivre(ManagerRegistry $doctrine, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $em = $doctrine->getManager();
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));

        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->removeSeries($serie);
        $em->flush();
        return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>1]);
    }

    #[Route('/suivre/{id}/{season}', name: 'suivre_serie')]
    public function suppr(ManagerRegistry $doctrine, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $em = $doctrine->getManager();
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->addSeries($serie);
        $em->flush();
        return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>1]);
    }

 
    
    #[Route('/{id}/{season}', name: 'app_series_show', methods: ['GET', 'POST'])]
    public function show(ManagerRegistry $doctrine, EpisodeRepository $repository, Series $series, EntityManagerInterface $entityManager, Season $season): Response
    {
        $em = $doctrine->getManager();
        $seasons = $entityManager
            ->getRepository(Season::class)
            ->findBy(array('series'=>$series->getId()), array('number'=>'ASC'));

        $ratings = $entityManager
            ->getRepository(Rating::class)
            ->findBy(array('series'=>$series->getId()), array('value' => 'ASC', 'date' => 'DESC'));
        
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($series->getId(), $season->getId());
        $tabTotaux = $this->totaux($ratings);
        $nombreSuiveur = $this->compterSuiveur($series->getUser());
        $notes = $this->totalNotes($ratings);
        $total = count($ratings);

        if ($total == 0) {
            $total = 1;
        }
        
        if (isset($_POST['titreSerie'])) {
            $series->setTitle($_POST['titreSerie']);
        }
        

        if (isset($_POST['plotSerie'])) {
            $series->setPlot($_POST['plotSerie']);
        }

        if (isset($_POST['dateDebut'])) {
            $series->setYearStart(intval($_POST['dateDebut']));
        }

        if (isset($_POST['dateFin'])) {
            $series->setYearEnd(intval($_POST['dateFin']));
        }

        if (isset($_POST['imdbSerie'])) {
            $series->setImdb($_POST['imdbSerie']);
        }

        if (isset($_POST['awardsSerie'])) {
            $series->setAwards($_POST['awardsSerie']);
        }

        if (isset($_POST['posterSerie'])) {
            $series->setPoster($_POST['posterSerie']);
        }

        if (isset($_POST['genreSerie'])) {
            $series->setPoster($_POST['genresSerie']);
        }
        $em->flush();
        return $this->render('series/show.html.twig', [
            'series' => $series,
            'seasons' => $seasons,
            'episode' => $episode,
            'currentSeason' => $season,
            'ratings' => $ratings,
            'moyenne' => $notes / $total,
            'total' => $total,
            'tabTotaux' => $tabTotaux,
            'nombreSuiveur' => $nombreSuiveur
        ]);
    }

    #[Route('/aVoir/{ep}/{id}/{season}', name: 'aVoir_episode')]
    public function aVoir(ManagerRegistry $doctrine, Episode $ep, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $em = $doctrine->getManager();
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));
        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->removeEpisode($ep);
        $em->flush();
        return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>$season->getId()]);
    }

    #[Route('/vu/{ep}/{id}/{season}', name: 'vu_episode')]
    public function vu(ManagerRegistry $doctrine,Episode $ep, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $em = $doctrine->getManager();
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));

        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        $this->getUser()->addEpisode($ep);
        $this->getUser()->addSeries($serie);
        $em->flush();
        return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>$season->getId()]);
    }

    #[Route('/vu/{id}/{season}', name: 'tout_les_episode_vus')]
    public function ToutMarquerVu(ManagerRegistry $doctrine, Series $serie, EntityManagerInterface $em, Season $season )
    {
        $em = $doctrine->getManager();
        $seasons = $em
            ->getRepository(Season::class)
            ->findBy(array('series'=>$serie->getId()), array('number'=>'ASC'));

        $repository = $em->getRepository(Episode::class);
        $episode = $repository->findEpisodes($serie->getId(), $season->getId());
        foreach ($episode as $ep){
            $this->getUser()->addEpisode($ep);
        }
        $em->flush();
        return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>$season->getId()]);
    }

    
}
