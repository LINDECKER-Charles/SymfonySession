<?php

namespace App\Controller;

use App\Entity\Intern;
use App\Entity\Module;
use App\Form\InternForm;
use App\Form\ModuleForm;
use App\Repository\InternRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class InternController extends AbstractController
{
    /**
     * Affiche la liste des stagiaires si l'utilisateur est admin.
     *
     * @param InternRepository $internRepository
     * @return Response
     */
    #[Route('/intern', name: 'app_intern')]
    public function index(Request $request, InternRepository $internRepository, PaginatorInterface $paginator): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())) {
            $query = $internRepository->createQueryBuilder('i')->getQuery();

            $pagination = $paginator->paginate(
                $query, // Requête Doctrine
                $request->query->getInt('page', 1), // Numéro de page
                10 // Nombre d'éléments par page
            );
 
            return $this->render('intern/index.html.twig', [
                'pagination' => $pagination,
            ]);   
        } else {
            return $this->redirectToRoute('app_home');  
        }
    }
    /**
     * Crée un nouveau stagiaire via un formulaire.
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $intern = new Intern();
        $form = $this->createForm(InternForm::class, $intern);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($intern);
            $em->flush();

            return $this->redirectToRoute('app_intern');
        }

        return $this->render('intern/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Affiche le profil d'un stagiaire avec ses sessions associées.
     * Redirige vers la connexion si l'utilisateur n'est pas connecté.
     *
     * @param int $id
     * @param InternRepository $internRepository
     * @return Response
     */
    #[Route('/profilS/{id}', name: 'profilS')]
    public function detailUser(int $id, InternRepository $internRepository): Response
    {
        $intern = $internRepository->find($id);

        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        return $this->render('intern/detail.html.twig', [
        'intern' => $intern,
        'sessions' => $intern->getSessions(),
        ]);
    }
}
