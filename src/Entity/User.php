<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity()]
class User implements UserInterface, PasswordHasherAwareInterface, PasswordAuthenticatedUserInterface
{

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Orm\Column(unique: true, nullable: false)]
    private string $email;

    #[Orm\Column(nullable: false)]
    private string $password;

    #[Orm\Column]
    private int $funds = 0;

    #[ORM\Column(nullable: true)]
    private string $firstName ;
    #[ORM\Column(nullable: true)]
    private string $lastName ;

    #[Orm\ManyToMany(targetEntity: Movie::class, inversedBy: 'users')]
    private Collection $movies;

    #[ORM\Column(length: 30, nullable: true)]
    private string $profilePictureUrl;

    /**
     * @return string
     */
    public function getProfilePictureUrl(): string
    {
        return $this->profilePictureUrl;
    }

    /**
     * @param string $profilePictureUrl
     */
    public function setProfilePictureUrl(string $profilePictureUrl): void
    {
        $this->profilePictureUrl = $profilePictureUrl;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return Collection
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    /**
     * @param Collection $movies
     */
    public function setMovies(Collection $movies): void
    {
        $this->movies = $movies;
    }

    public function addMovie(Movie $movie): void
    {
        $this->movies[] = $movie;
    }

    /**
     * @return int
     */
    public function getFunds(): int
    {
        return $this->funds;
    }

    /**
     * @param int $funds
     */
    public function setFunds(int $funds): void
    {
        $this->funds = $funds;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function addFunds(int $addedFunds): void
    {
        $this->funds += $addedFunds;
    }



    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPasswordHasherName(): ?string
    {
        return 'bcrypt_algorithm';
    }
}