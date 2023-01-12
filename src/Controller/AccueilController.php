<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Series;
use App\Repository\RealSeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil_index', methods: ['POST','GET'])]
    public function index(ManagerRegistry $doctrine,RealSeriesRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Series::class);
        $id=$repository->getAllId();
        $dixId = [];
        while(count($dixId)<10){
            $nb = rand(0,count($id)-1); // random pour selectionner aléatoirement un id
            $row =$id[$nb]; // la ligne concernée par le random
            $colonne = $row['id']; // uniquement l'id 
            array_push($dixId,$colonne); // on ajoute l'id
            $dixId = array_unique($dixId); // on supprime les doublons
        }
        $dixSeries=$repository->findDixSeries($dixId[0],$dixId[1],$dixId[2],$dixId[3],$dixId[4],$dixId[5],$dixId[6],$dixId[7],$dixId[8],$dixId[9]);
        return $this->render('accueil/index.html.twig', [
            'series' => $dixSeries,
        ]);
    }
    #[Route('/poster/{id}', name: 'app_accueil_poster', methods: ['GET'])]
    public function getPoster(Series $series): Response
    {
        return new Response(stream_get_contents($series->getPoster()),200,array('Content-type'=>'image/jpeg'));
    }
}
