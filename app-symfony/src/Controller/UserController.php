<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    /**
     * Affiche la liste des utilisateurs si l'utilisateur connecté est admin.
     *
     * @param UserRepository $userRepository Le repository des utilisateurs.
     * @return Response Vue avec tous les utilisateurs ou redirection.
     */
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository, Request $request
    , PaginatorInterface $paginator): Response
    {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $query = $userRepository->createQueryBuilder('i')->getQuery();

            $pagination = $paginator->paginate(
                $query, // Requête Doctrine
                $request->query->getInt('page', 1), // Numéro de page
                10 // Nombre d'éléments par page
            );
 
            return $this->render('user/index.html.twig', [
                'pagination' => $pagination,
            ]);   
        } else {
            return $this->redirectToRoute('app_home');  
        }
    }

    /**
     * Affiche le profil détaillé d’un utilisateur selon son ID.
     * Redirige vers la connexion si aucun utilisateur n’est connecté.
     *
     * @param int $id ID de l'utilisateur à afficher.
     * @param UserRepository $userRepository Le repository des utilisateurs.
     * @return Response Vue du profil utilisateur ou redirection vers login.
     */
    #[Route('/profil/{id}', name: 'profil')]
    public function detailUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/detail.html.twig', [
        'user' => $user,
        'sessions' => $user->getSessions(),
        ]);
    }
}
