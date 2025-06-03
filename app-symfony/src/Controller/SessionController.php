<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Form\SessionForm;
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

final class SessionController extends AbstractController
{
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

    #[Route('/creationSession', name: 'creation_session')]
    public function creationSession(Request $request, SessionForm $sessionForm, SessionRepository $sessionRepository, EntityManagerInterface $entityManager): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }



        $session = new Session();
        $form = $this->createForm(SessionForm::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $sessionRepository->findOneBy(['sessionName' => $session->getSessionName()]);

            if (($session->getNbPlaceReserved() > $session->getNbPlaceTt()) || ($session->getNbPlaceTt() < 0 || $session->getNbPlaceReserved() < 0) || $session->getStartDate() > $session->getEndDate()) {
                return $this->render('session/add.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

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

    #[Route('/session/edit/{id}', name: 'edit_session')]
    public function edit(Session $session, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SessionForm::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('detail_module', ['id' => $session->getId()]);
        }

        return $this->render('session/add.html.twig', [
            'form' => $form,
            'editMode' => true,
        ]);
    }

    #[Route('/session/{id}', name: 'detail_session')]public function detailSession(int $id, SessionRepository $sessionRepository, InternRepository $internRepository, ModuleRepository $moduleRepository): Response
    {
        $session = $sessionRepository->find($id);

        if (!$session) {
            throw $this->createNotFoundException('Session introuvable.');
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

    #[Route('/session/add-programme/{id}', name: 'add_programme', methods: ['POST'])]
    public function addProgramme(
        Request $request,
        Session $session,
        SessionRepository $sessionRepository,
        ModuleRepository $moduleRepository,
        EntityManagerInterface $em
    ): Response {
        $moduleId = $request->request->get('module');
        $nbDay = $request->request->get('nbDay');

        $maxDay = $session->getStartDate()->diff($session->getEndDate())->days;
        $ttModule = $moduleRepository->findTtDays($em, $session->getId());

        if(($maxDay - $nbDay - $ttModule) < 0){
            $this->addFlash('error', 'Nombre de jour total de la session dépassé');
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        if (!$moduleId || !$nbDay) {
            $this->addFlash('error', 'Module ou nombre de jours manquant.');
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        $module = $moduleRepository->find($moduleId);
        if (!$module) {
            $this->addFlash('error', 'Module introuvable.');
            return $this->redirectToRoute('detail_session', ['id' => $session->getId()]);
        }

        $existingProgramme = $em->getRepository(Programme::class)->findOneBy([
            'session' => $session,
            'module' => $module,
        ]);

        if ($existingProgramme) {
            // On additionne la durée
            $existingProgramme->setNbDay($existingProgramme->getNbDay() + (int) $nbDay);
            $this->addFlash('info', 'Durée du module mise à jour dans la session.');
            $em->flush();
        } else {
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

    #[Route('/programme/delete/{id}', name: 'delete_programme', methods: ['POST'])]
    public function deleteProgramme(Programme $programme, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $programme->getId(), $request->request->get('_token'))) {
            $em->remove($programme);
            $em->flush();
        }

        return $this->redirectToRoute('detail_session', [
            'id' => $programme->getSession()->getId()
        ]);
    }

    #[Route('/session/add_intern/{idS}/{idI}', name: 'add_intern')]
    public function addIntern(int $idS, int $idI, SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $em): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        $session = $sessionRepository->find($idS);
        $intern = $internRepository->find($idI);

        $session->addIntern($intern);
        $em->persist($session);
        $em->flush();

        return $this->redirectToRoute('detail_session', ['id' => $idS]);


    }

    #[Route('/session/del_intern/{idS}/{idI}', name: 'del_intern')]
    public function delIntern(int $idS, int $idI, SessionRepository $sessionRepository, InternRepository $internRepository, EntityManagerInterface $em): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        $session = $sessionRepository->find($idS);
        $intern = $internRepository->find($idI);

        $session->removeIntern($intern);
        $em->persist($session);
        $em->flush();

        return $this->redirectToRoute('detail_session', ['id' => $idS]);
    }
}
