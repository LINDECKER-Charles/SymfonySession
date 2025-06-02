<?php

namespace App\Form;

use App\Entity\Intern;
use App\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    $builder
        ->add('interName', null, ['label' => 'Nom'])
        ->add('internSex', null, ['label' => 'Sexe'])
        ->add('internCity', null, ['label' => 'Ville'])
        ->add('internCp', null, ['label' => 'Code postal'])
        ->add('internAdress', null, ['label' => 'Adresse'])
        ->add('internBirth', null, ['label' => 'Date de naissance'])
        ->add('internEmail', null, ['label' => 'Email']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intern::class,
        ]);
    }
}
