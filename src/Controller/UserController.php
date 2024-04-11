<?php

namespace App\Controller;

use App\Entity\Request\RegisterUserRequest;
use App\Entity\User;
use App\Form\Type\RegisterUserRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    #[Route(path:'/user/add_funds', methods: ['POST'])]
    public function addFunds(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $funds = $request->request->get('funds');

        if (empty($funds) || $funds <= 0) {
            return new JsonResponse(['err' => 'valoare egala cu 0 sau negativa']);
        }

        $email = $this->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $user->addFunds($funds);

        $entityManager->flush();
        return new JsonResponse(['msg' => 'fonduri alocate cu succes']);

    }

    #[Route(path:'/user/register', name: 'app_user_register', methods: ['POST'])]
    public function registerUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $data = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'checkPassword' => $request->request->get('check_password'),
            'firstName' => $request->request->get('first_name'),
            'lastName' => $request->request->get('last_name')
        ];

        $registerUserRequest = new RegisterUserRequest($entityManager);
        $form = $this->createForm(RegisterUserRequestType::class, $registerUserRequest);
        $form->submit($data);

        if (!$form->isValid()) {
            return new JsonResponse(
                [
                    'err' => $form->getErrors(true)->current()->getOrigin()->getName() . ' - ' .
                        $form->getErrors(true)->current()->getMessage()
                ]);
        } else {
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $hashedPassword = $hasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['msg' => 'utilizator adaugat cu succes']);
        }
    }

}