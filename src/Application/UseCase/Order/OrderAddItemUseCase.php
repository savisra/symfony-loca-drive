<?php

namespace App\Application\UseCase\Order;

use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Model\User;
use App\Domain\Model\Order;
use App\Domain\Model\Vehicle;
use App\Domain\Model\Rental;
use DateTimeImmutable;
use App\Domain\Model\OrderStatus;
use Exception;

class OrderAddItemUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        int $vehicleId,
        User $customer,
        string $startDate,
        string $endDate,
        string $pickupLocation
    ) {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($vehicleId);
        if (!$vehicle) {
            throw new Exception("No vehicle found with ID $vehicleId");
        }

        $order = $this->entityManager->getRepository(Order::class)->findOrderByCustomer($customer);
        if (!$order) {
            $order = new Order($customer);
            $this->entityManager->persist($order);
        }

        if ($order->getStatus() !== OrderStatus::CART) {
            throw new Exception("Cannot add a Rental to an Order that is not in the cart anymore");
        }

        $rental = new Rental(
            $vehicle,
            $order,
            null,
            new DateTimeImmutable($startDate),
            new DateTimeImmutable($endDate),
            $pickupLocation
        );
        $order->addRental($rental);

        $this->entityManager->persist($rental);
        $this->entityManager->flush();

        return $rental->getId();
    }
}