<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Form\SessionForm;
use App\Service\PdfService;
use App\Repository\UserRepository;
use App\Repository\InternRepository;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\VarDumper\VarDumper;

final class SessionController extends AbstractController
{
    /**
     * Affiche la liste des sessions selon le rôle de l'utilisateur.
     *
     * @param SessionRepository $sessionRepository
     * @return Response
     */
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
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
    /**
     * Permet la création d'une session (admin ou formateur uniquement).
     *
     * @param Request $request
     * @param SessionForm $sessionForm
     * @param SessionRepository $sessionRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/creationSession', name: 'creation_session')]
    public function creationSession(Request $request, SessionForm $sessionForm, SessionRepository $sessionRepository, EntityManagerInterface $entityManager): Response
    {
        /* On vérifie les permissions */
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }

        /* Création d'une nouvelle session et formulaire */
        $session = new Session();
        $form = $this->createForm(SessionForm::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            /* On récupère la session si elle existe déjà */
            $existing = $sessionRepository->findOneBy(['sessionName' => $session->getSessionName()]);
            /* On vérifie si la range du nombre de place est juste */
            if (($session->getNbPlaceReserved() > $session->getNbPlaceTt()) || ($session->getNbPlaceTt() < 0 || $session->getNbPlaceReserved() < 0) || $session->getStartDate() > $session->getEndDate()) {
                return $this->render('session/add.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
            /* Si il existe déjà on envoie un erreur */
            if ($existing) {
                $form->addError(new FormError('Session déjà existante'));
            } else {
                foreach ($session->getInterns() as $intern) {
                    $intern->addSession($session);
                }
                    $session->addUser($this->getUser());
                    $entityManager->persist($session);
                    $entityManager->flush();

                    return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
            }
        }

        return $this->render('session/add.html.twig', [
            'form' => $form->createView(),
            'editMode' => false,
        ]);
    }
    /**
     * Modifie une session existante.
     *
     * @param Session $session
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/session/edit/{id}', name: 'edit_session')]
    public function edit(Session $session, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SessionForm::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (($session->getNbPlaceReserved() > $session->getNbPlaceTt()) || ($session->getNbPlaceTt() < 0 || $session->getNbPlaceReserved() < 0) || $session->getStartDate() > $session->getEndDate()) {
                return $this->render('session/add.html.twig', [
                    'form' => $form->createView(),
                    'editMode' => true,
                ]);
            }
            $em->flush();
            /* var_dump($em);die(); */
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        return $this->render('session/add.html.twig', [
            'form' => $form,
            'editMode' => true,
        ]);
    }
    /**
     * Affiche le détail d’une session : stagiaires, modules, utilisateurs liés.
     *
     * @param int $id
     * @param SessionRepository $sessionRepository
     * @param InternRepository $internRepository
     * @param ModuleRepository $moduleRepository
     * @return Response
     */
    #[Route('/session/{id}', name: 'detail_session')]public function detailSession(int $id, SessionRepository $sessionRepository, InternRepository $internRepository, ModuleRepository $moduleRepository): Response
    {
        $session = $sessionRepository->find($id);

        if (!$session) {
            /* throw $this->createNotFoundException('Session introuvable.'); */
            return $this->redirectToRoute('app_home');
        }

        $interns = $session->getInterns()->toArray();
        usort($interns, fn($a, $b) => strcmp($a->getInterName(), $b->getInterName()));

        $notInterns = $internRepository->findByNotInSession($session->getId());
        usort($notInterns, fn($a, $b) => strcmp($a->getInterName(), $b->getInterName()));

        return $this->render('session/detail.html.twig', [
            'session' => $session,
            'users' => $session->getUsers(),
            'interns' => $interns,
            'notInterns' => $notInterns,
            'modules' => $moduleRepository->findAll(),
        ]);
    }
    /**
     * Ajoute un module à une session, si la durée restante le permet.
     *
     * @param Request $request
     * @param Session $session
     * @param SessionRepository $sessionRepository
     * @param ModuleRepository $moduleRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/session/add-programme/{id}', name: 'add_programme', methods: ['POST'])]
    public function addProgramme(
        Request $request,
        Session $session,
        SessionRepository $sessionRepository,
        ModuleRepository $moduleRepository,
        EntityManagerInterface $em
    ): Response {
        /* On récupère dans les post le module et le nombre de jour */
        $moduleId = $request->request->get('module');
        $nbDay = $request->request->get('nbDay');
        /* On fait les calcules pour vérifier que ça ne dépasse pas la duré total de la formation */
        $maxDay = $session->getStartDate()->diff($session->getEndDate())->days;
        $ttModule = $moduleRepository->findTtDays($em, $session->getId());
        /* On fait différente vérification(duré, existance des val) */
        if(($maxDay - $nbDay - $ttModule) < 0){
            $this->addFlash('error', 'Nombre de jour total de la session dépassé');
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        if (!$moduleId || !$nbDay) {
            $this->addFlash('error', 'Module ou nombre de jours manquant.');
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }
        /* On récupère le module */
        $module = $moduleRepository->find($moduleId);
        if (!$module) {
            $this->addFlash('error', 'Module introuvable.');
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }
        /* On vérifie si le programme existe déjà */
        $existingProgramme = $em->getRepository(Programme::class)->findOneBy([
            'session' => $session,
            'module' => $module,
        ]);
        /* S'il existe on l'edit pour rajouter la durer en + */
        if ($existingProgramme) {
            // On additionne la durée
            $existingProgramme->setNbDay($existingProgramme->getNbDay() + (int) $nbDay);
            $this->addFlash('info', 'Durée du module mise à jour dans la session.');
            $em->flush();
        } else {
            /* Sinon on crée un nouveau programme */
            // Nouveau programme
            $programme = new Programme();
            $programme->setSession($session);
            $programme->setModule($module);
            $programme->setNbDay((int)$nbDay);

            $em->persist($programme);
            $em->flush();
            $this->addFlash('success', 'Module ajouté à la session.');
        }

        return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
    }
    /**
     * Supprime un programme d'une session après vérification du CSRF token.
     *
     * @param Programme $programme
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/programme/delete/{id}', name: 'delete_programme', methods: ['POST'])]
    public function deleteProgramme(Programme $programme, Request $request, EntityManagerInterface $em): Response
    {
        /* On récupère le programme et on le suprimme etttt on check le tokencsrf avant évidament */
        if ($this->isCsrfTokenValid('delete' . $programme->getId(), $request->request->get('_token'))) {
            $em->remove($programme);
            $em->flush();
        }

        return $this->redirectToRoute('detail_session', [
            'id' => $programme->getSession()->getId()
        ]);
    }
    /**
     * Ajoute un stagiaire à une session (admin ou formateur uniquement).
     *
     * @param int $idS
     * @param int $idI
     * @param SessionRepository $sessionRepository
     * @param InternRepository $internRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/session/add_intern/{idS}/{idI}', name: 'add_intern')]
    public function addIntern(int $idS, int $idI, SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $em): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        /* On récupère la session et l'interne */
        $session = $sessionRepository->find($idS);
        $intern = $internRepository->find($idI);
        /* On ajoute l'interne a la session */
        $session->addIntern($intern);
        $em->persist($session);
        $em->flush();

        return $this->redirectToRoute('detail_session', ['id' => $idS]);


    }
    /**
     * Supprime un stagiaire d'une session (admin ou formateur uniquement).
     *
     * @param int $idS
     * @param int $idI
     * @param SessionRepository $sessionRepository
     * @param InternRepository $internRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/session/del_intern/{idS}/{idI}', name: 'del_intern')]
    public function delIntern(int $idS, int $idI, SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $em): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        /* On récupère la session et l'interne */
        $session = $sessionRepository->find($idS);
        $intern = $internRepository->find($idI);

        /* On supprime l'interne a la session */
        $session->removeIntern($intern);
        $em->persist($session);
        $em->flush();

        return $this->redirectToRoute('detail_session', ['id' => $idS]);
    }
    /**
     * Génère un PDF d'attestation pour chaque stagiaire d’une session.
     *
     * @param int $id
     * @param SessionRepository $sessionRepository
     * @param PdfService $pdfServic
     * @param ModuleRepository $moduleRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/session/generate/{id}', name: 'generate_pdf_session')]
    public function generatePdfSession(int $id, SessionRepository $sessionRepository, PdfService $pdfServic, ModuleRepository $moduleRepository, EntityManagerInterface $em): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        /* On récupère : la session, les stagiaires dedans et le programme */
        $session = $sessionRepository->find($id);
        $interns = $session->getInterns();
        $programmes = $session->getProgrammes();

        /* Pour chaque stagiaire on génère le contenue puis le PDF */
        foreach($interns as $intern){
            $html = '
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    h1 { color: #3B4C66; text-align: center; }
                    .section { margin-bottom: 20px; }
                    .label { font-weight: bold; }
                    ul { padding-left: 20px; }
                </style>

                <h1>Attestation de Formation</h1>

                <div class="section">
                    <p><span class="label">Nom du stagiaire :</span> ' . $intern->getInterName() . '</p>
                    <p><span class="label">Formation :</span> ' . $session->getSessionName() . '</p>
                    <p><span class="label">Durée :</span> ' . $session->getStartDate()->format('d/m/Y') . ' au ' . $session->getEndDate()->format('d/m/Y') . '</p>
                    <p><span class="label">Total jours :</span> ' . $moduleRepository->findTtDays($em, $id) . ' jours</p>
                </div>

                <div class="section">
                    <p><span class="label">Contenu de la formation :</span></p>
                    <ul>';
                foreach ($programmes as $module) {
                        $html .= '<li>' . $module->getModule()->getMudleName() . ' – ' . $module->getNbDay() . ' jour(s)</li>';
                    }
                $html .= '
                    </ul>
                </div>

                <p style="text-align:right;">Fait le ' . date('d/m/Y') . '</p>
                ';

            $pdfServic->generatePdf($html, $intern->getInterName(), $session->getSessionName());
        }

        return $this->redirectToRoute('detail_session', ['id' => $id]);
    }


}
