<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Entity\Role;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{
    #[Route('/account/new', name: 'account')]
    #[Route('/account/edit/{id}', name: 'account-edit')]
    public function accountForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $update = false;
        $idRoleOriginal = null;

        // infos session (comme ton cours)
        $filter = $request->getSession()->get('filter');
        $idRoleConnected = $filter['idRole'] ?? 999;
        $idUserConnected = $filter['idUser'] ?? 0;

        if ($request->attributes->get('_route') === 'account-edit') {
            $update = true;

            $idToEdit = (int) $request->attributes->get('id');

            // droit : admin (idRole=1) OU lui-même
            if (!($idRoleConnected == 1 || $idUserConnected == $idToEdit)) {
                return $this->redirectToRoute('index');
            }

            $user = $entityManager->getRepository(User::class)->find($idToEdit);
            if (!$user) {
                return $this->redirectToRoute('index');
            }

            $idRoleOriginal = $user->getRole()?->getId();
        } else {
            $user = new User();
        }

        $userForm = $this->createForm(UserType::class, $user, [
            'editMode' => $update,
            'idRoleConnected' => $idRoleConnected
        ]);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();

            if (!$update) {
                // création : hash le password
                $passHashed = password_hash($user->getPassword(), PASSWORD_BCRYPT);
                $user->setPassword($passHashed);
            } else {
                // edit : si role disabled => il arrive null => on remet l'ancien
                if ($user->getRole() === null && $idRoleOriginal !== null) {
                    $role = $entityManager->getRepository(Role::class)->find($idRoleOriginal);
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
