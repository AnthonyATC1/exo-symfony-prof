<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Role;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class UsersController extends AbstractController
{
    #[Route('/account/new', name: 'account')]
    public function accountForm(Request $request, EntityManagerInterface $manager): Response
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => 'login'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'password'
                ]
            ])
            ->add('mail', EmailType::class, [
                'attr' => [
                    'placeholder' => 'xxx@mail.com'
                ]
            ])
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
            ])
            ->getForm();


        return $this->render('view/auth/account.html.twig', [
            'formAccount' => $form->createView()
        ]);
    }

}
