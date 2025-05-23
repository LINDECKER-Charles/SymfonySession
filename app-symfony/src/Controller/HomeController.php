<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\InternRepository;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ModuleRepository $moduleRepository, InternRepository $internRepository, UserRepository $userRepository,  SessionRepository $sessionRepository): Response
    {
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
    }
}
