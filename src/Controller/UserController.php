<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager,PaginatorInterface $paginator, Request $request): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        if (isset($_POST['mail'])) {
            $users = $repository->findUser($_POST['mail']);
        } else {
            $users = $repository->findBy([], ['registerDate'=>'DESC']);
        }
        $usersPagines = $paginator->paginate(
            $users, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            8 // Nombre de résultats par page
            );

        return $this->render('user/index.html.twig', [
            'users' => $usersPagines,
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

    #[Route('/{userID}', name: 'app_user_ficheUtilisateur', methods: ['GET', 'POST'])]
    public function ficheUtilisateur(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, User $userID): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $userID]);
        $repositoryCountry = $em->getRepository(Country::class);
        
        if(isset($_GET['nom'])){
            $user->setName($_GET['nom']);
        }
        if(isset($_GET['pays'])){
            $pays = $repositoryCountry->findOneByName($_GET['pays']);
            $user->setCountry($pays);
        }
        $em->flush();
        return $this->render('user/fiche_utilisateur.html.twig', [
            'user' => $user,
        ]); 
    }
    #[Route('/editer/{userID}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function editer(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, User $userID): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);

        $user = $repository->findOneBy(['id' => $userID]);
        $repositoryCountry = $em->getRepository(Country::class);
        $pays = $repositoryCountry->findAll();
        return $this->render('user/editer.html.twig', [
            'user' => $user,
            'pays' =>$pays
        ]); 
    }
}
