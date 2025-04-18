<?php

namespace App\Application\UseCase\Order;

use App\Domain\Model\User;
use App\Domain\Model\Rental;
use App\Domain\Model\Order;
use App\Domain\Model\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class OrderRemoveInsuranceUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        int $rentalId,
        User $customer
    ) {
        $rental = $this->entityManager->getRepository(Rental::class)->find($rentalId);
        if (!$rental) {
            throw new Exception("Rental not found with ID $rentalId");
        }

        // Check that insurance isn't already set
        if (!$rental->hasInsurance()) {
            throw new Exception("Rental $rentalId has no insurance");
        }

        // Get user-related Order and check its status is CART
        $order = $this->entityManager->getRepository(Order::class)->findOrderByCustomer($customer);
        if (!$order) {
            throw new Exception("No Order found for customer with ID " . $customer->getId());
        }
        if ($order->getStatus() !== OrderStatus::CART) {
            throw new Exception("Cannot remove an Insurance from an order which status is not CART");
        }
        if ($rental->getOrder()->getId() !== $order->getId()) {
            throw new Exception("The rental does not belong in Customer's cart");
        }

        $rental->clearInsurance();
        $order->determinePrice();

        $this->entityManager->flush();
    }
}