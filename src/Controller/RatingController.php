<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Series;
use App\Form\RatingType;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rating')]
class RatingController extends AbstractController
{
    #[Route('/', name: 'app_rating_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $ratings = $entityManager
            ->getRepository(Rating::class)
            ->findAll();

        return $this->render('rating/index.html.twig', [
            'ratings' => $ratings,
        ]);
    }

    #[Route('/new', name: 'app_rating_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $time = new \DateTime('@'.strtotime('now'));

            $rating = new Rating();
            $user = $entityManager->getRepository(User::class)->findOneBy(['id'=>$_POST['user']]);
            $serie = $entityManager->getRepository(Series::class)->findOneBy(['id'=>$_POST['series']]);

            $rating->setUser($user);
            $rating->setSeries($serie);
            $rating->setComment($_POST['text']);
            $rating->setValue($_POST['note']);
            $rating->setDate($time);

            $entityManager->persist($rating);
            $entityManager->flush();
        } catch(Exception) {
            $error = "Vous avez déjà commenté cette série";
            return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>1]);
        }

        return $this->redirectToRoute('app_series_show',['id'=>$serie->getId(),'season'=>1]);
    }

    #[Route('/{id}', name: 'app_rating_show', methods: ['GET'])]
    public function show(Rating $rating): Response
    {
        return $this->render('rating/show.html.twig', [
            'rating' => $rating,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rating_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rating $rating, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rating/edit.html.twig', [
            'rating' => $rating,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rating_delete', methods: ['POST'])]
    public function delete(Request $request, Rating $rating, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rating->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rating);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rating_index', [], Response::HTTP_SEE_OTHER);
    }
}
