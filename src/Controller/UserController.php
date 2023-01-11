<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        if (isset($_POST['mail'])) {
            $users = $repository->findUser($_POST['mail']);
        } else {
            $users = $repository->findBy([], ['registerDate'=>'DESC']);
        }
        

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }  

    #[Route('/', name: 'app_user_profil_index', methods: ['GET', 'POST'])]
    public function profil(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        if (isset($_POST['mail'])) {
            $em = $doctrine->getManager();
            $repository = $em->getRepository(User::class);
            $users = $repository->findUser($_POST['mail']);
        } else {
            $users = null;
        }

        return $this->render('user/index_profil.html.twig', [
            'users' => $users,
        ]);
    }   
}
