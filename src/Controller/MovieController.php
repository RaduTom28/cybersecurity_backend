<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController
{
    #[Route(path:'/movies', name: 'app_movies')]
    public function getAllMovies(EntityManagerInterface $entityManager): Response
    {
        return new JsonResponse($entityManager->getRepository(Movie::class)->findAll());
    }
}