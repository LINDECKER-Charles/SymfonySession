<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $users = $userRepository->findAll();
            return $this->render('user/index.html.twig', [
                'users' => $users,
            ]);   
        }else{
            return $this->redirectToRoute('app_home');  
        } 
    }

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
