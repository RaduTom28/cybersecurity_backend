<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Request\BuyMovieRequest;
use App\Entity\Request\ViewMovieRequest;
use App\Form\Type\BuyMovieRequestType;
use App\Form\Type\ViewMovieRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieController extends AbstractController
{
    #[Route(path:'/movie/all', name: 'app_movies')]
    public function getAllMovies(EntityManagerInterface $entityManager): Response
    {

        $movies = $entityManager->getRepository(Movie::class)->findAll();
        $responseData = array();

        foreach ($movies as $movie) {
            $responseData[] = [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'price' => $movie->getPrice(),
                'poster_image_url' => $movie->getPosterImageUrl()
            ];
        }
            
        return new JsonResponse($responseData);
    }
    #[Route(path: '/movie/like', name: 'app_movie_like')]
    public function findMovieLike(Request $request, EntityManagerInterface $entityManager): Response
    {
        $param = $request->query->get('title');

        $conn = $entityManager->getConnection();


        $sql = '
            SELECT * FROM movie m 
            WHERE title LIKE "%'. $param . '%";';

        $resultSet = $conn->executeQuery($sql);
        $res = $resultSet->fetchAllAssociative();

        //dd($res);

        return new JsonResponse($res);
    }

    #[Route(path:'/movie/view', name: 'app_movie_view')]
    public function viewMovie(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $data = ['movieId' => $request->query->get('movie_id')];
        $viewMovieRequest = new ViewMovieRequest($entityManager, $tokenStorage);
        $form = $this->createForm(ViewMovieRequestType::class, $viewMovieRequest);

        $form->submit($data);

        if (!$form->isValid()) {
            return new JsonResponse(['err' => 'Film invalid']);
        } else {
            return new JsonResponse(['msg' => 'Vizionare placuta']);
        }
    }

    #[Route(path:'/movie/buy', name: 'app_movie_buy', methods: ['POST'])]
    public function buyMovie(Request $request,
                             EntityManagerInterface $entityManager,
                             TokenStorageInterface $tokenStorage): Response
    {
        $movieId = $request->request->get('movie_id');
        $data = ['movieId' => $movieId];

        $buyMovieRequest = new BuyMovieRequest($entityManager, $tokenStorage);
        $form = $this->createForm(BuyMovieRequestType::class, $buyMovieRequest);
        $form->submit($data);

        if (!$form->isValid()) {
            return new JsonResponse(
                [
                    'err' => $form->getErrors(true)->current()->getOrigin()->getName() . ' - ' .
                    $form->getErrors(true)->current()->getMessage()
                ]);
        } else {
            $user = $this->getUser();
            $movie = $entityManager->getRepository(Movie::class)->find($movieId);

            $user->addMovie($movie);
            $user->setFunds($user->getFunds() - $movie->getPrice());

            $entityManager->flush();

            return new JsonResponse(['msg' => 'Film cumparat cu succes']);
        }
    }

    #[Route(path: '/movie/collection', name: 'app_movie_collection')]
    public function getCollection(): Response
    {
        $collection = $this->getUser()->getMovies();
        $responseData = array();

        foreach ($collection as $movie) {
            $responseData[] = [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'price' => $movie->getPrice(),
                'poster_image_url' => $movie->getPosterImageUrl()
            ];
        }

        return new JsonResponse($responseData);
    }

}