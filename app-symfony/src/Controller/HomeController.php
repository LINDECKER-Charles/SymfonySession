<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\InternRepository;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ModuleRepository $moduleRepository, InternRepository $internRepository, UserRepository $userRepository,  SessionRepository $sessionRepository): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $modules = $moduleRepository->findAll();
            $interns = $internRepository->findAll();
            $users = $userRepository->findAll();
            $sessions = $sessionRepository->findAll();
            return $this->render('home/index.html.twig', [
                'modules' => $modules,
                'interns' => $interns,
                'users' => $users,
                'sessions' => $sessions,
            ]);   
        }else{
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $sessions = $user->getSessions();
            

            $modules = [];
            foreach ($user->getSessions() as $session) {
                foreach ($session->getProgrammes() as $programme) {
                    $module = $programme->getModule();
                    if ($module && !in_array($module, $modules, true)) {
                        $modules[] = $module;
                    }
                }
            }

            return $this->render('home/index.html.twig', [
                'modules' => $modules,
                'sessions' => $sessions,
            ]);   
        } 

        /* dd($re->getSession()); */


    }
}
