<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeriesType;
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
    #[Route('/', name: 'app_series_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $series = $entityManager
            ->getRepository(Series::class)
            ->findAll();

        return $this->render('series/index.html.twig', [
            'series' => $series,
        ]);
    }
    
    #[Route('/{id}/{season}', name: 'app_series_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine,SeriesRepository $repository, Series $series, EntityManagerInterface $entityManager, Season $season): Response
    {
        $render = $entityManager
            ->getRepository(Season::class)
            ->findBy(array('series'=>$series->getId()), array('number'=>'ASC'));
        
        $renderEpisode = $entityManager
            ->getRepository(Episode::class)
            ->findBy(array('season'=>$season->getNumber()), array());

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
