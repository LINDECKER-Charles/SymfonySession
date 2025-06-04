<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EmploieController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function getEvents(SessionRepository $sessionRepo): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse([], 401);
        }

        $events = [];

        foreach ($user->getSessions() as $session) {
            $events[] = [
                'title' => $session->getSessionName(),
                'start' => $session->getStartDate()->format('Y-m-d'),
                'end' => $session->getEndDate()->format('Y-m-d'),
                'url' => $this->generateUrl('detail_session', ['id' => $session->getId()]),
            ];
        }



        return new JsonResponse($events);
    }
}