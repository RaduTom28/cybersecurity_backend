<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    #[Route(path:'/user/add_funds', methods: ['POST'])]
    public function addFunds(Request $request, EntityManagerInterface $entityManager): Response
    {

        $funds = $request->request->get('funds');

        if (empty($funds) || $funds <= 0) {
            return new JsonResponse(['err' => 'valoare egala cu 0 sau negativa']);
        }

        $email = $this->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $user->addFunds($funds);

        return new JsonResponse(['msg' => 'fonduri alocate cu succes']);

    }
}