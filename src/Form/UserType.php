<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $editMode = $options['editMode'];
        $idRoleConnected = $options['idRoleConnected'];

        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => 'Login',
                    'readonly' => $editMode
                ]
            ]);

        // En création uniquement : password + confirm_password
        if (!$editMode) {
            $builder
                ->add('password', PasswordType::class)
                ->add('confirm_password', PasswordType::class, [
                    'mapped' => false,
                    'label' => 'Confirm password'
                ]);
        }

        $builder
            ->add('mail', EmailType::class, [
                'attr' => ['placeholder' => 'xxx@mail.com']
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                // en edit : si user connecté n'est pas admin, on bloque le select
                'disabled' => ($editMode && $idRoleConnected > 1)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'editMode' => false,
            'idRoleConnected' => 999,
        ]);
    }
}
