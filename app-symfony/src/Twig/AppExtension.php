<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * Déclare les fonctions Twig personnalisées disponibles dans les templates.
     *
     * @return array Liste des fonctions Twig, ici une pour vérifier l'existence d'un fichier.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('file_exists', [$this, 'fileExists']),
        ];
    }

    /**
     * Vérifie si un fichier existe à un chemin donné.
     *
     * @param string $path Le chemin relatif ou absolu du fichier.
     * @return bool True si le fichier existe, False sinon.
     */
    public function fileExists(string $path): bool
    {   
        return file_exists('' . ltrim($path, '/'));
    }
}
