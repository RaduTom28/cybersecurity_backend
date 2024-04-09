<?php

namespace App\Entity\Request;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterUserRequest
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    #[Assert\NotBlank]
    private string $lastName;
    #[Assert\NotBlank]
    private string $firstName;
    #[Assert\NotBlank()]
    #[Assert\Email]
    private string $email;

    #[Assert\NotBlank]
    private string $password;

    #[Assert\NotBlank]
    private string $checkPassword;

    #[Assert\Callback()]
    public function checkExistsUser(ExecutionContextInterface $context)
    {
        if (empty($this->email)) {
            return;
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);

        if (!empty($user)) {
            $context->buildViolation('Exista deja un utilizator cu acest email')
                ->atPath('/user/register')
                ->addViolation();
        }

    }

    #[Assert\Callback]
    public function checkMatchingPasswords(ExecutionContextInterface $context)
    {
        if (empty($this->password) || empty($this->checkPassword)) {
            return;
        }

        if ($this->password != $this->checkPassword) {
            $context->buildViolation('Parolele nu sunt identice')
                ->atPath('/user/register')
                ->addViolation();
        }
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
     * @return string
     */
    public function getCheckPassword(): string
    {
        return $this->checkPassword;
    }


    /**
     * @param string $checkPassword
     */
    public function setCheckPassword(string $checkPassword): void
    {
        $this->checkPassword = $checkPassword;
    }

}