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

class OrderSetPaymentMethodUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        string $paymentMethod,
        User $customer
    ) {
        // Get user-related Order and check its status is CART
        $order = $this->entityManager->getRepository(Order::class)->findOrderByCustomer($customer);
        if (!$order) {
            throw new Exception("No Order found for customer with ID " . $customer->getId());
        }
        if ($order->getStatus() !== OrderStatus::CART) {
            throw new Exception("Cannot set Payment Method to an order which status is not CART");
        }
        
        $paymentMethodValue = null;
        try {
            $paymentMethodValue = PaymentMethod::from($paymentMethod);
        } catch (ValueError $e) {
            // Graceful fallback
            $paymentMethodValue = PaymentMethod::CARD;
        }

        $order->setPaymentMethod($paymentMethodValue);
        $order->setStatus(OrderStatus::PAYMENT_METHOD_SET);

        $this->entityManager->flush();
    }
}