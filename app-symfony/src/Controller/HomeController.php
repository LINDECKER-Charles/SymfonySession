<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\InternRepository;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use App\Service\RechercheService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{

    /**
     * Affiche la page d’accueil selon le rôle :
     * - Admin : toutes les entités (modules, users, stagiaires, sessions).
     * - Autre utilisateur : ses sessions et modules associés.
     *
     * @param ModuleRepository $moduleRepository
     * @param InternRepository $internRepository
     * @param UserRepository $userRepository
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    #[Route('/home', name: 'app_home')]
    public function index(ModuleRepository $moduleRepository, InternRepository $internRepository, UserRepository $userRepository,  SessionRepository $sessionRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
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
    /**
     * Redirige vers la route de la page d’accueil.
     *
     * @return Response
     */
    #[Route('/', name: 'redirect_home')]
    public function redirectHome(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Recherche les utilisateurs dont le nom commence par une chaîne donnée.
     *
     * Cette fonction effectue une requête sur la base de données pour trouver les 
     * utilisateurs dont le champ `name` commence par la valeur de `$param`, 
     * en ignorant la casse (majuscule/minuscule).
     * 
     * @param string $param La chaîne de recherche (ex: "Cha" pour trouver "Charles").
     * @param UserRepository $userRepository Le repository utilisateur pour interagir avec la base.
     * 
     * @return JsonResponse Retourne une liste d'utilisateurs sous forme de JSON ou un message d'erreur.
     */
    #[Route('/app_search/{param}', name: 'app_search', methods: ['GET'])]
    public function search(string $param, UserRepository $userRepository, SessionRepository $sessionRepository, /* EntityManager $em */): JsonResponse
    {
        /* Requet DQL pour trouver un name qui commence par param */
        $users = $userRepository->findUsersByNamePrefix($param);
        $sessions = $sessionRepository->findSessionsByNamePrefix($param);

        /* Si aucun utilisateurs trouvé alors on renvoie une erreur */
        if (!$users  && !$sessions) {
            return new JsonResponse(['error' => 'Donnée trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Transformation des objets User en tableau JSON
        $userResults = array_map(fn($user) => ['id' => $user->getId(), 'name' => $user->getName()], $users);
        $sessionResults = array_map(fn($session) => ['id' => $session->getId(), 'sessionName' => $session->getSessionName()], $sessions);

    /* Retourne les résultats combinés */
    return new JsonResponse([
        'users' => $userResults,
        'sessions' => $sessionResults
    ]);
    }
    /**
     * Affiche la page de recherche avec les sessions et utilisateurs.
     *
     * @param UserRepository $userRepository Le repository des utilisateurs.
     * @param SessionRepository $sessionRepository Le repository des sessions.
     * @return Response La vue twig contenant les résultats initiaux.
     */
    #[Route('/recherche', name: 'app_recherche')]
    public function recherche(UserRepository $userRepository,  SessionRepository $sessionRepository): Response
    {
        /* On récupère touts les utilisateurs et sessions */
        $users = $userRepository->findAll();
        $sessions = $sessionRepository->findAll();

        return $this->render('home/recherche.html.twig', [
            'users' => $users,
            'sessions' => $sessions
        ]);   
    }
    /**
     * Effectue une recherche AJAX filtrée sur les utilisateurs et sessions.
     *
     * @param string $param Les paramètres JSON encodés dans l’URL.
     * @param UserRepository $userRepository Le repository des utilisateurs.
     * @param SessionRepository $sessionRepository Le repository des sessions.
     * @return JsonResponse Un tableau de résultats filtrés au format JSON.
     */
    #[Route('/recherche/{param}', name: 'app_recherche_ajax')]
    public function rechercheAjax(string $param, UserRepository $userRepository, SessionRepository $sessionRepository, RechercheService $rechercheService): JsonResponse
    {
        return new JsonResponse($rechercheService->filterInput($param, $userRepository, $sessionRepository));
    }
}
