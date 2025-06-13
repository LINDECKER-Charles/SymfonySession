<?php

namespace App\Service;

use App\Repository\SessionRepository;
use App\Repository\UserRepository;

class RechercheService
{

    
    public function filterInput(string $param, UserRepository $userRepository, SessionRepository $sessionRepository): array
    {
        $data = json_decode($param, true);
        $search = strtolower(trim($data['search'] ?? '')); //on ignore la cast en mettant en minuscule
            /* On récupère les autre donnés */
        $utilisateurs = $data['utilisateurs'] ?? false;
        $sessions = $data['sessions'] ?? false;
        $ordre = ($data['ordre'] ?? true) ? 'ASC' : 'DESC';
        $max = $data['maxRes'] ?? 100;


        $results = [];

        /* Si on a des utilisateurs */
        if ($utilisateurs) {
            /* Requet DQL a déplacer dans le repot */
            $users = $userRepository->findUsersByNameOrderMax($ordre, $search, $max);

            /* Pour chaque utilisateurs on déplace dans la variable de résultat final */
            foreach ($users as $u) {
                $results[] = [
                    'type' => 'user',
                    'id' => $u->getId(),
                    'name' => $u->getName(),
                    'email' => $u->getEmail(),
                ];
            }
        }

        if ($sessions) {
            /* Requet DQL a déplacer dans le repot */
            $sessions = $sessionRepository->findSessionsByNameOrderMax($ordre, $search, $max);
            /* Pour chaque sessions on déplace dans la variable de résultat final */
            foreach ($sessions as $s) {
                $results[] = [
                    'type' => 'session',
                    'id' => $s->getId(),
                    'name' => $s->getSessionName(),
                    'places' => ($s->getNbPlaceReserved() + count($s->getInterns())) . '/' . $s->getNbPlaceTt()
                ];
            }
        }

        return $results;
    }
}