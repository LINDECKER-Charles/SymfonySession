<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\InternRepository;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(ModuleRepository $moduleRepository, InternRepository $internRepository, UserRepository $userRepository,  SessionRepository $sessionRepository): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $sessions = $sessionRepository->findAll();
            return $this->render('session/index.html.twig', [
                'sessions' => $sessions,
            ]);   
        }else{
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $sessions = $user->getSessions();
            return $this->render('session/index.html.twig', [
                'sessions' => $sessions,
            ]);   
        } 
    }

    #[Route('/session/{id}', name: 'detail_session')]
    public function detailSession(int $id, SessionRepository $sessionRepository): Response
    {
        $session = $sessionRepository->find($id);

        if (!$session) {
            throw $this->createNotFoundException('Session introuvable.');
        }

        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'users' => $session->getUsers(),
            'interns' => $session->getInterns(),
        ]);
    }

}
