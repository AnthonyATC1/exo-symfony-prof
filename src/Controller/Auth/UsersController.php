<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UsersController extends AbstractController
{
    #[Route('/account/new', name: 'account')]
    #[Route('/account/edit/{id}', name: 'account-edit')]
    public function accountForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $update = false;

        if ($request->attributes->get('_route') === 'account-edit') {

            $filter = $request->getSession()->get('filter');
            $idRoleUser = $filter['idRole'];
            $idUser = $filter['idUser'];

            if ($idUser == $request->attributes->get('id') || $idRoleUser == 1) {

                $user = $entityManager
                    ->getRepository(User::class)
                    ->find($request->attributes->get('id'));

                if (!$user) {
                    return $this->redirectToRoute('index');
                }

                $idRole = $user->getRole()->getId();

            } else {
                return $this->redirectToRoute('index');
            }

            $update = true;

        } else {
            $user = new User();
        }

        $userForm = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('mail', EmailType::class)
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
            ])
            ->getForm();

        if ($update) {
            $userForm->remove('password');
        }

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $user = $userForm->getData();

            if (!$update) {
                $passHashed = password_hash($user->getPassword(), PASSWORD_BCRYPT);
                $user->setPassword($passHashed);
            } else {
                if ($user->getRole() === null) {
                    $role = $entityManager
                        ->getRepository(Role::class)
                        ->find($idRole);

                    $user->setRole($role);
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('view/auth/account.html.twig', [
            'formAccount' => $userForm->createView(),
            'editMode' => $update
        ]);
    }
}
