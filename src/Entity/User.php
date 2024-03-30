<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity()]
class User implements UserInterface, PasswordHasherAwareInterface
{
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

    #[Orm\ManyToMany(targetEntity: Movie::class, inversedBy: 'users')]
    private Collection $movies;

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