<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JwtService;
use App\Service\KeyManagementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class AuthenticationController extends AbstractController
{
    public function __construct(private readonly string $publicKey)
    {
    }

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

    #[Route(path:'/public_key', name: 'app_public_key')]
    public function getPublicKey(KeyManagementService $keyManagementService): Response
    {
        return new JsonResponse($keyManagementService->generatePublicKeyResponseBody($this->publicKey));
    }

    #[Route(path:'/', name: 'app_home')]
    public function home(): Response
    {
        $user = $this->getUser();
        return new JsonResponse(
            [
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'funds' => $user->getFunds()
            ]
        );
    }

}