<?php

// src/Controller/LogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * Affiche les lignes du fichier de log contenant une tentative de spam détectée via le honeypot.
     *
     * @return Response
     */
    #[Route('/logs', name: 'logs')]
    public function show(): Response
    {
        $path = $this->getParameter('kernel.logs_dir') . '/dev.log';
        $lines = file_exists($path) ? file($path, FILE_IGNORE_NEW_LINES) : [];

        $logs = array_filter($lines, fn($line) => str_contains($line, 'Tentative de spam détectée'));

        return $this->render('security/honeypot.html.twig', ['logs' => array_reverse($logs)]);
    }
}
