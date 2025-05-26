<?php

namespace App\Controller;

use App\Repository\InternRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InternController extends AbstractController
{
    #[Route('/intern', name: 'app_intern')]
    public function index(InternRepository $internRepository): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $interns = $internRepository->findAll();
            return $this->render('intern/index.html.twig', [
                'interns' => $interns,
            ]);   
        }else{
            return $this->redirectToRoute('app_home');  
        }
    }
}
