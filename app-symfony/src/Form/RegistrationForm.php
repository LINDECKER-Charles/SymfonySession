<?php

namespace App\Form;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use App\Form\EventListener\HoneypotSubscriber;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class RegistrationForm extends AbstractType
{
    private LoggerInterface $logger;
    private RequestStack $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }
    
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('email')
        ->add('name', TextType::class, [
            'label' => 'Nom complet',
        ])
        ->add('sex', ChoiceType::class, [
            'choices' => [
                'Homme' => 'Homme',
                'Femme' => 'Femme',
                'Autre' => 'Autre',
            ],
            'placeholder' => 'Choisissez',
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
        ])
        ->add('cp', TextType::class, [
            'label' => 'Code postal',
        ])
        ->add('adress', TextType::class, [
            'label' => 'Adresse',
        ])
        ->add('birth', DateType::class, [
            'label' => 'Date de naissance',
            'widget' => 'single_text',
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [ 
                new IsTrue([
                    'message' => 'Vous devez accepter les conditions.',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('confirmPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank(['message' => 'Confirmer votre mot de passe']),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères',
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('captcha', Recaptcha3Type::class, [
            'constraints' => new Recaptcha3(),
            'action_name' => 'nom_action',
        ])
        ->add('honeytrap', TextType::class, [
            'mapped' => false,
            'required' => false,
            'attr' => [
                'autocomplete' => 'off',
                'tabindex' => -1,
                'aria-hidden' => 'true',
                'style' => 'position:absolute;left:-9999px;', // ou display:none si tu veux
            ],
        ])
        ->addEventSubscriber(new HoneypotSubscriber($this->logger, $this->requestStack));
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, 
        ]);
    }
}
