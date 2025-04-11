<?php

namespace App\Application\UseCase\Order;

use App\Domain\Model\Insurance;
use App\Domain\Model\User;
use App\Domain\Model\Rental;
use App\Domain\Model\Order;
use App\Domain\Model\OrderStatus;
use App\Domain\Model\PaymentMethod;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ValueError;

class OrderPayUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        User $customer
    ) {
        // Get user-related Order and check its status is CART
        $order = $this->entityManager->getRepository(Order::class)->findOrderByCustomer($customer);
        if (!$order) {
            throw new Exception("No Order found for customer with ID " . $customer->getId());
        }

        switch ($order->getStatus()) {
            case OrderStatus::CART:
                throw new Exception("You must set a payment method before paying");
                break;
            case OrderStatus::PAID:
                throw new Exception("This order has already been paid for");
                break;
        }

        if (mt_rand(1, 10) === 1) {
            throw new Exception("Broke ass. Payment declined.");
        }
    
        $order->setStatus(OrderStatus::PAID);

        $this->entityManager->flush();
    }
}