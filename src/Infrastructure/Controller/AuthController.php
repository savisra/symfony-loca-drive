<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Authentication\LoginUseCase;
use App\Application\UseCase\Authentication\RegisterCustomerUseCase;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth')]
final class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request, LoginUseCase $loginUseCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(['error' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $loginUseCase->execute($data['email'], $data['password']);
            return $this->json($user);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(Request $request, RegisterCustomerUseCase $registerCustomerUseCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = ['email', 'first_name', 'last_name', 'driving_license_issue_date', 'password'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => 'Missing required field: ' . $field], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $userId = $registerCustomerUseCase->execute(
                $data['email'],
                $data['first_name'],
                $data['last_name'],
                $data['driving_license_issue_date'],
                $data['password']
            );

            return $this->json(
                [
                    'message' => 'User registered successfully',
                    'userId' => $userId
                ],
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
