<?php

namespace App\Entity\Request;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Movie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class BuyMovieRequest
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenStorageInterface $tokenStorage
    )
    {
    }

    #[Assert\NotNull]
    private string $movieId;

    #[Assert\Callback()]
    public function checkExistsMovie(ExecutionContextInterface $context)
    {
        if (empty($this->movieId)) {
            return;
        }

        $movie = $this->entityManager->getRepository(Movie::class)->find($this->movieId);

        if (empty($movie)) {
            $context->buildViolation('Filmul nu a fost gasit in baza de date')
                ->atPath('movieId')
                ->addViolation();
        }
    }

    #[Assert\Callback]
    public function checkAlreadyOwned(ExecutionContextInterface $context)
    {
        if (empty($this->movieId)) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $movie = $this->entityManager->getRepository(Movie::class)->find($this->movieId);

        if (empty($movie)) {
            return;
        }

        $ownedMovies = $user->getMovies()->getValues();

        if (in_array($movie, $ownedMovies)) {
            $context->buildViolation('Ati cumparat deja acest film')
                ->atPath('movieId')
                ->addViolation();
        }

    }

    #[Assert\Callback]
    public function checkAffordability(ExecutionContextInterface $context)
    {
        if (empty($this->movieId)) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $movie = $this->entityManager->getRepository(Movie::class)->find($this->movieId);

        if (empty($movie)) {
            return;
        }

        if ($movie->getPrice() > $user->getFunds()) {
            $context->buildViolation('Nu aveti fonduri suficiente pentru a cumpara filmul')
                ->atPath('movieId')
                ->addViolation();
        }

    }
    /**
     * @return string
     */
    public function getMovieId(): string
    {
        return $this->movieId;
    }

    /**
     * @param string $movieId
     */
    public function setMovieId(string $movieId): void
    {
        $this->movieId = $movieId;
    }

}