<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    #[Route('/login', name:'app_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, JwtService $jwtService): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $jwt = $jwtService->generateJwtForUser($user);

        return new JsonResponse([
            'user' => $user->getUserIdentifier(),
            'token' => $jwt
        ]);
    }

    #[Route(path:'/', name: 'app_home')]
    public function home(): Response
    {
        dd($this->getUser());
        return new Response($this->getUser());
    }

}