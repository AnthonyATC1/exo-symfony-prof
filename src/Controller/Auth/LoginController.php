<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    private string $title = 'Login';

    #[Route('/login', name: 'login')]
    public function login(
        Request $request,
        EntityManagerInterface $manager,
        SessionInterface $session
    ): Response
    {
        $req = $request->request;

        if (
            $req->count() > 0 &&
            $req->get('username') !== null &&
            $req->get('password') !== null
        ) {
            $user = $manager
                ->getRepository(User::class)
                ->findOneByLowerUsername($req->get('username'));

            if (
                $user !== null &&
                password_verify($req->get('password'), $user->getPassword())
            ) {

                $this->title = 'Bienvenue ' . $user->getUsername() . ' sur la première page';

                $session->set('filter', [
                    'idRole'   => $user->getRole()->getId(),
                    'username' => $user->getUsername(),
                    'idUser'   => $user->getId()
                ]);

                return $this->render('view/index.html.twig', [
                    'title' => $this->title
                ]);
            }
        }

        return $this->render('view/index.html.twig', [
            'title' => 'Bienvenue sur la première page',
            'errorLogin' => 'Error Login/Password'
        ]);
    }
    #[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session): Response
    {
        // Supprime le tableau de session
        $session->remove('filter');

        // Retour à la page index
        return $this->render('view/index.html.twig', [
            'title' => 'Tout semble en ordre !'
        ]);
    }

}
