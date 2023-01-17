<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Country;
use App\Entity\Rating;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function ficheUtilisateur(ManagerRegistry $doctrine,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, User $userID): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $userID]);
        $repositoryCountry = $em->getRepository(Country::class);
        $ratings = $entityManager
            ->getRepository(Rating::class)
            ->getRatingById($userID);
        if(isset($_GET['nom'])){
            $user->setName($_GET['nom']);
            $em->flush();
        }
        if(isset($_GET['pays'])){
            $pays = $repositoryCountry->findOneByName($_GET['pays']);
            $user->setCountry($pays);
            $em->flush();
        }
        if(isset($_POST['nouveauMdp']) && isset($_POST['confirmation']) ){
            if($_POST['confirmation'] != $_POST['nouveauMdp']){
                return $this->render('user/changeMdp.html.twig', [
                    'user' => $user,
                    'nonValide' => "Mot de passe pas identique",
                ]); 
            }
            else{
                $user->setPassword($userPasswordHasher->hashPassword($user,$_POST['nouveauMdp']));
                $em->flush();
                return $this->render('user/fiche_utilisateur.html.twig', [
                    'user' => $user,
                    'mesAvis' => $ratings
                ]);   
            }   
        }
        
        return $this->render('user/fiche_utilisateur.html.twig', [
            'user' => $user,
            'mesAvis' => $ratings
        ]); 
    }

    #[Route('/list/{userID}', name: 'app_user_ban', methods: ['GET', 'POST'])]
    public function bannir(ManagerRegistry $doctrine, EntityManagerInterface $entityManager,PaginatorInterface $paginator, Request $request, User $userID): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        $userABannir = $repository->findOneBy(['id' => $userID]);
        if ($userABannir->getBanni() == 0){
            $userABannir->setBanni(1);
        }
        else {
            $userABannir->setBanni(0);
        }
        $entityManager->persist($userABannir);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_index');
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
    #[Route('/nouveauMdp/{userID}', name: 'app_mdp_edit', methods: ['GET', 'POST'])]
    public function motDePasse(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, User $userID): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(['id' => $userID]);
        
        return $this->render('user/changeMdp.html.twig', [
            'user' => $user,
            'nonValide' => ""
        ]); 
    }
}
