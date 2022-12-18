<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => [
                    'placeholder' => 'Email',
                ],
                'label' => false,
            ])
            ->add('firstname', null, [
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
                'label' => false,
            ])
            ->add('lastname', null, [
                'attr' => [
                    'placeholder' => 'Nom',
                ],
                'label' => false,
            ])
            ->add('phoneNumber', null, [
                'attr' => [
                    'placeholder' => 'Numéro',
                ],
                'label' => false,
            ])
            ->add('country', null, [
                'attr' => [
                    'placeholder' => 'Pays',
                ],
                'label' => false,
            ])
            ->add('adress', null, [
                'attr' => [
                    'placeholder' => 'Addresse',
                ],
                'label' => false,
            ])
            ->add('zipCode', null, [
                'attr' => [
                    'placeholder' => 'Code postal',
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
