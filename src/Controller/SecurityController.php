<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm($token, UserRepository $userRepository, TokenStorageInterface $tokenStorage)
    {
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if ($user) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');
            $this->getDoctrine()->getManager()->flush();

            // Automatically login
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
            $tokenStorage->setToken($token);
        }

        return $this->render('security/confirmation.html.twig', [
            'user' => $user
        ]);
    }
}