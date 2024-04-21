<?php

namespace App\Entity\Request;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Movie;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ViewMovieRequest 
{

    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenStorageInterface $tokenStorage
    )
    {
    }

    #[Assert\NotBlank]
    private int $movieId;

    #[Assert\Callback]
    public function checkOwnedMovie(ExecutionContextInterface $context)
    {
        if (empty($this->movieId)) {
            return;
        }

        $loggedUserEmail = $this->tokenStorage->getToken()->getUser()->getUserIdentifier();
        $loggedUser= $this->entityManager->getRepository(User::class)->findOneBy(['email' => $loggedUserEmail]);

        $ownedMovies = $loggedUser->getMovies();
        $requestedMovie = $this->entityManager->getRepository(Movie::class)->find($this->movieId);

        if (!$ownedMovies->contains($requestedMovie)) {
            $context->buildViolation('Nu detineti acest film')
                ->atPath('movie/view')
                ->addViolation();
        }

    }


    public function setMovieId (int $movieId): void
    {
        $this->movieId = $movieId;
    }

    public function getMovieId (): int 
    {
        return $this->movieId;
    }

}