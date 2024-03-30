<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    #[Route('/login', name:'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('login.html.twig', [
              'last_username' => $lastUsername,
              'error'         => $error,
          ]);
    }

    #[Route(path:'/', name: 'app_home')]
    public function home(): Response
    {
        dd($this->getUser());
        return new Response($this->getUser());
    }

}