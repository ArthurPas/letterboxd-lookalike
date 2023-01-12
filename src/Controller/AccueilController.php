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
        for($i=0;$i<10;$i++){
            array_push($dixId,$id[rand(0,count($id)-1)]);
        }
        $dixSeries=$repository->findDixSeries($dixId[0],$dixId[1],$dixId[2],$dixId[3],$dixId[4],$dixId[5],$dixId[6],$dixId[7],$dixId[8],$dixId[9]);
        return $this->render('accueil/index.html.twig', [
            'series' => $dixSeries,
        ]);
    }
    
}
