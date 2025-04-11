<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Rental\RentalCreationUseCase;
use App\Application\UseCase\Rental\RentalDeletionUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

#[Route('/rental')]
final class RentalController extends AbstractController
{
    #[Route('/create', name: 'app_rental_create', methods: ['POST'])]
    public function create(Request $request, RentalCreationUseCase $rentalCreationUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        $required = ['vehicle_id', 'start_date', 'end_date', 'pickup_location'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => 'Missing required field: ' . $field], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $rentalId = $rentalCreationUseCase->execute(
                $data['vehicle_id'],
                $this->getUser(),
                $data['start_date'],
                $data['end_date'],
                $data['pickup_location']
            );

            return $this->json(
                [
                    'message' => 'Rental created successfully',
                    'rental_id' => $rentalId
                ],
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete', name: 'app_rental_delete', methods: ['DELETE'])]
    public function deletion(Request $request, RentalDeletionUseCase $rentalDeletionUseCase): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rental_id'])) {
            return $this->json(['error' => 'Missing required field: rental_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $rentalDeletionUseCase->execute(
                $this->getUser(),
                $data['rental_id']
            );

            return $this->json(
                [
                    'message' => 'Rental deleted successfully',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
