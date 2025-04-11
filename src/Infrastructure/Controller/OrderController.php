<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Order\OrderAddInsuranceUseCase;
use App\Application\UseCase\Order\OrderAddItemUseCase;
use App\Application\UseCase\Order\OrderPayUseCase;
use App\Application\UseCase\Order\OrderRemoveInsuranceUseCase;
use App\Application\UseCase\Order\OrderRemoveItemUseCase;
use App\Application\UseCase\Order\OrderSetPaymentMethodUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route('/add_item', name: 'app_order_add_item', methods: ['POST'])]
    public function add_item(Request $request, OrderAddItemUseCase $orderAddItemUseCase): JsonResponse
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
            $rentalId = $orderAddItemUseCase->execute(
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

    #[Route('/remove_item', name: 'app_order_remove_item', methods: ['DELETE'])]
    public function remove_item(Request $request, OrderRemoveItemUseCase $orderRemoveItemUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rental_id'])) {
            return $this->json(['error' => 'Missing required field: rental_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $orderRemoveItemUseCase->execute(
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

    #[Route('/add_insurance', name: 'app_order_add_insurance', methods: ['PATCH'])]
    public function add(Request $request, OrderAddInsuranceUseCase $orderAddInsuranceUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rental_id'])) {
            return $this->json(['error' => 'Missing required field: rental_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $orderAddInsuranceUseCase->execute(
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

    #[Route('/remove_insurance', name: 'app_order_remove_insurance', methods: ['PATCH'])]
    public function remove(Request $request, OrderRemoveInsuranceUseCase $orderRemoveInsuranceUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['rental_id'])) {
            return $this->json(['error' => 'Missing required field: rental_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $orderRemoveInsuranceUseCase->execute(
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

    #[Route('/set_payment_method', name: 'app_order_set_payment_method', methods: ['PATCH'])]
    public function set_payment_method(Request $request, OrderSetPaymentMethodUseCase $orderSetPaymentMethodUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['payment_method'])) {
            return $this->json(['error' => 'Missing required field: payment_method'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $orderSetPaymentMethodUseCase->execute(
                $data['payment_method'],
                $this->getUser(),
            );

            return $this->json(
                [
                    'message' => 'Payment method set successfully',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/pay', name: 'app_order_pay', methods: ['PATCH'])]
    public function pay(Request $request, OrderPayUseCase $orderPayUseCase): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $orderPayUseCase->execute(
                $this->getUser(),
            );

            return $this->json(
                [
                    'message' => 'Order paid!',
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}