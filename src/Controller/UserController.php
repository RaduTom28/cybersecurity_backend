<?php

namespace App\Controller;

use App\Entity\Request\RegisterUserRequest;
use App\Entity\User;
use App\Form\Type\RegisterUserRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{

    public function __construct(private readonly string $publicKey)
    {
    }

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

    #[Route(path:'/user/my', name: 'app_user_my')]
    public function getUserData(Request $request, EntityManagerInterface $entityManager): Response
    {
        //no authenticator
        $authHeader = $request->headers->get('Authorization');

        if (empty($authHeader)) {
            return new JsonResponse(['err' => 'No Auhtorization header provided']);
        }

        $splitHeader = explode(" ",$authHeader);

        $bearer = $splitHeader[0];
        $token = $splitHeader[1];

        if (empty($bearer) || empty($token) || ($bearer != 'Bearer')) {
            return new JsonResponse(['err' => 'Bad header format']);
        }

        try {
            $decoded = (array) JWT::decode($token, new Key($this->publicKey, 'RS256'));
            $conn = $entityManager->getConnection();

            $sql = "
            SELECT * FROM user u
            WHERE email = '". $decoded['email'] ."';";


            $resultSet = $conn->executeQuery($sql);
            $res = $resultSet->fetchAllAssociative();
            return new JsonResponse($res);
        } catch (\Exception $e) {
            Return new JsonResponse(['err' => $e->getMessage()]);
        }



    }

}