<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Vehicle\VehicleCreationUseCase;
use App\Application\UseCase\Vehicle\VehicleDeletionUseCase;
use App\Application\UseCase\Vehicle\VehicleEditionUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

#[Route('/vehicle')]
final class VehicleController extends AbstractController
{
    #[Route('/create', name: 'app_vehicle_create', methods: ['POST'])]
    public function create(Request $request, VehicleCreationUseCase $vehicleCreationUseCase): JsonResponse
    {
        // Deny if not admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'dégage'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        $required = ['model', 'brand', 'daily_rate'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => 'Missing required field: ' . $field], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $carId = $vehicleCreationUseCase->execute(
                $data['model'],
                $data['brand'],
                $data['daily_rate'],
            );

            return $this->json(
                [
                    'message' => 'Car created successfully',
                    'carId' => $carId
                ],
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update', name: 'app_vehicle_update', methods: ['PATCH'])]
    public function edit(Request $request, VehicleEditionUseCase $vehicleEditionUseCase): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'dégage'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['vehicle_id'])) {
            return $this->json(['error' => 'Missing required field: vehicle_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vehicleEditionUseCase->execute(
                $data['vehicle_id'],
                $data['model'],
                $data['brand'],
                $data['daily_rate'],
            );

            return $this->json(
                [
                    'message' => 'Car edited successfully',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete', name: 'app_vehicle_delete', methods: ['DELETE'])]
    public function delete(Request $request, VehicleDeletionUseCase $vehicleDeletionUseCase): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'dégage'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['vehicle_id'])) {
            return $this->json(['error' => 'Missing required field: vehicle_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vehicleDeletionUseCase->execute(
                $data['vehicle_id']
            );

            return $this->json(
                [
                    'message' => 'Car deleted successfully',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }        
    }
}
