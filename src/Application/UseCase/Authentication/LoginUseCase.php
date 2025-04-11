<?php

namespace App\Application\UseCase\Authentication;

use App\Domain\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginUseCase
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function execute(string $email, string $password) 
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new AuthenticationException('User does not exist');
        }

        if (!$this->userPasswordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'first_name' => $user->getFirstname(),
            'last_name' => $user->getLastname(),
            'driving_license_issue_date' => $user->getDrivingLicenseIssueDate()
        ];
    }
}