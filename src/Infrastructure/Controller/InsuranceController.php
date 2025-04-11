<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Insurance\AddInsuranceToRentalUseCase;
use App\Application\UseCase\Insurance\RemoveInsuranceFromRentalUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

#[Route('/insurance')]
final class InsuranceController extends AbstractController
{
    #[Route('/add', name: 'app_insurance_add', methods: ['POST'])]
    public function add(Request $request, AddInsuranceToRentalUseCase $addInsuranceToRentalUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rental_id'])) {
            return $this->json(['error' => 'Missing required field: rental_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $addInsuranceToRentalUseCase->execute(
                $data['rental_id'],
                $this->getUser(),
            );

            return $this->json(
                [
                    'message' => 'Insurance added successfully',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/remove', name: 'app_insurance_remove', methods: ['PATCH'])]
    public function remove(Request $request, RemoveInsuranceFromRentalUseCase $removeInsuranceFromRentalUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rental_id'])) {
            return $this->json(['error' => 'Missing required field: rental_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $removeInsuranceFromRentalUseCase->execute(
                $data['rental_id'],
                $this->getUser(),
            );

            return $this->json(
                [
                    'message' => 'Insurance removed successfully',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
