<?php

namespace App\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vehicle')]
final class VehicleController extends AbstractController
{
    #[Route('/create', name: 'app_vehicle_create')]
    public function create(Request $request): JsonResponse
    {
        // Deny if not admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'EAU PUTIN SA MARCHE PA'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VehicleController.php',
        ]);
    }
}
