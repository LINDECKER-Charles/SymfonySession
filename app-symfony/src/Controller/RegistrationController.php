<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    /**
     * Gère l'enregistrement d'un nouvel utilisateur.
     * Vérifie le honeypot, la correspondance et la robustesse du mot de passe.
     * Connecte automatiquement l'utilisateur après inscription.
     *
     * @param Request $request Requête HTTP contenant les données du formulaire.
     * @param UserPasswordHasherInterface $userPasswordHasher Service de hachage de mot de passe.
     * @param Security $security Service de sécurité pour la connexion automatique.
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine.
     * @param ParameterBagInterface $params Accès aux paramètres de configuration.
     * @param LoggerInterface $logger Logger pour détecter un spam via honeypot.
     * @return Response
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, ParameterBagInterface $params, LoggerInterface $logger): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('app_home');
        }
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        
        
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('honeytrap')->getData())) {
                // probable bot
                $logger->info('Tentative spam détectée via honeypot.');
                return $this->redirectToRoute('app_register');
            }
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if($plainPassword !== $confirmPassword){
                $form->get('plainPassword')->addError(new \Symfony\Component\Form\FormError('Le mot de passe confirmé est différent.'));
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]);
            }
            if(preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/', $plainPassword)){
                $form->get('plainPassword')->addError(new \Symfony\Component\Form\FormError('Mot de passe trop faible.'));
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]); 
            }
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'recaptcha_site_key' => $params->get('karser_recaptcha3.site_key'),
        ]);
    }
}
