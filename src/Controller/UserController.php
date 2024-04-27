<?php

namespace App\Controller;

use App\Entity\Request\ProfilePicUploadRequest;
use App\Entity\Request\RegisterUserRequest;
use App\Entity\User;
use App\Form\Type\ProfilePicUploadRequestType;
use App\Form\Type\RegisterUserRequestType;
use App\Service\FileUploader;
use App\Service\ImageLocatorService;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use phpDocumentor\Reflection\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

        try {
            $funds = (int) $funds;
        } catch (\Exception $e) {
            return new JsonResponse(['err' => 'Valoare invalida']);
        }

        if (empty($funds) || $funds <= 0 || $funds >= 1000) {
            return new JsonResponse(['err' => 'valoare invalida']);
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
            return new JsonResponse(['err' => 'No Auhtorization header provided'], Response::HTTP_UNAUTHORIZED);
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
            SELECT email, funds, last_name, first_name, profile_picture_url FROM user u
            WHERE email = '". $decoded['email'] ."';";

            $resultSet = $conn->executeQuery($sql);
            $res = $resultSet->fetchAllAssociative();

            if (empty($res[0]['profile_picture_url'])) {
                $res[0]['profile_picture_url'] = '/images/default_profile.png';
            } else {
                $res[0]['profile_picture_url'] = '/images/'.$res[0]['profile_picture_url'];
            }

            return new JsonResponse($res);
        } catch (\Exception $e) {
            Return new JsonResponse(['err' => $e->getMessage()]);
        }
    }

    #[Route(path: '/user/profile_pic/get', name: 'app_profile_pic_get', methods: ['GET'])]
    public function getProfilePic(ImageLocatorService $imageLocator, EntityManagerInterface $entityManager): Response
    {
        $email = $this->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $profilePic = $user->getProfilePictureUrl();

        if (empty($profilePic)) {
            return new JsonResponse(['name' => '/images/default_profile.png']);
        };

        $profilePicUrl = explode('.',$user->getProfilePictureUrl());

        $res = $imageLocator->locateImage($profilePicUrl[0]);
        return new JsonResponse($res);
    }

    #[Route(path: '/user/profile_pic/upload', name: 'app_profile_pic_upload', methods: ['POST'])]
    public function uploadProfilePic(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager): Response
    {
        $file = $request->files->get('profile_pic');
        if (empty($file)) {
            return new JsonResponse(['err' => 'No file provided']);
        }

        $form = $this->createForm(ProfilePicUploadRequestType::class, new ProfilePicUploadRequest());
        $form->submit(['profile_pic' => $file]);

        if($form->isValid()) {
            try {
                $fileName = $fileUploader->upload($form->get('profile_pic')->getData());
            } catch (FileException $exception){
                return new JsonResponse(['err' => 'fisierul nu a putut fi uploadat']);
            }

            $loggedUserEmail = $this->getUser()->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $loggedUserEmail]);
            $user->setProfilePictureUrl($fileName);
            $entityManager->flush();

            return new JsonResponse(['msg' => 'Upload success !']);
        }

        return new JsonResponse(['err' => $form->getErrors(true)->current()->getMessage()]);


    }
}