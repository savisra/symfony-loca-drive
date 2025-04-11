<?php

namespace App\Application\UseCase\Order;

use App\Domain\Model\Rental;
use App\Domain\Model\User;
use App\Domain\Model\Order;
use App\Domain\Model\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class OrderRemoveItemUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        User $customer,
        int $rentalId
    ) {
        $rental = $this->entityManager->getRepository(Rental::class)->find($rentalId);
        if (!$rental) {
            throw new Exception("Rental not found with ID $rentalId");
        }

        // Get user-related Order and check its status is CART
        $order = $this->entityManager->getRepository(Order::class)->findOrderByCustomer($customer);
        if (!$order) {
            throw new Exception("No Order found for customer with ID " . $customer->getId());
        }
        if ($order->getStatus() !== OrderStatus::CART) {
            throw new Exception("Cannot remove a Rental from an order which status is not CART");
        }

        if ($rental->getOrder()->getId() !== $order->getId()) {
            throw new Exception("The rental does not belong in Customer's cart");
        }

        $order->removeRental($rental);
        $this->entityManager->flush();
    }
}