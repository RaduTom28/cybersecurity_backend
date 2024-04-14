<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/test', name: 'app_test')]
    public function test(Request $request, EntityManagerInterface $entityManager): Response
    {

        //test sql injection

        $param = $request->query->get('param');

        $conn = $this->entityManager->getConnection();


        $sql = '
            SELECT * FROM movie m 
            WHERE title = "'. $param . '";';

        $resultSet = $conn->executeQuery($sql);
        $res = $resultSet->fetchAllAssociative();

        dd($res);

        return new Response('test');
    }

    #[Route('/test_secure', name: 'app_test_secure')]
    public function testSecure(Request $request, EntityManagerInterface $entityManager): Response
    {

        //test sql injection

        $param = $request->query->get('param');

        $conn = $this->entityManager->getConnection();



        $sql = '
            SELECT * FROM movie m 
            WHERE title = :param ;';

        $resultSet = $conn->executeQuery($sql,['param' => $param]);
        $res = $resultSet->fetchAllAssociative();

        dd($res);

        return new Response('test');
    }

}