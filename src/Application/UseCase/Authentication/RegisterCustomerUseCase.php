<?php

namespace App\Application\UseCase\Authentication;

use App\Domain\Model\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterCustomerUseCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function execute(
        string $email,
        string $firstname,
        string $lastname,
        string $drivingLicenseIssueDate,
        string $password
    ): int {
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new Exception('Email already in use');
        }

        $user = new User(
            $email,
            $password,
            $firstname,
            $lastname,
            new DateTimeImmutable($drivingLicenseIssueDate),
            'ROLE_CUSTOMER',
            $this->userPasswordHasher
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    }
}