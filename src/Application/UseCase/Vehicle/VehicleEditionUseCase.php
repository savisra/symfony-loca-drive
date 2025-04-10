<?php

namespace App\Application\UseCase\Vehicle;

use App\Domain\Model\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class VehicleEditionUseCase
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
        string $model,
        string $brand,
        float $dailyRate
    ) {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($vehicleId);

        if (!$vehicle) {
            throw new Exception('No vehicle found with ID ' . $vehicleId);
        }

        $vehicle->setModel($model);
        $vehicle->setBrand($brand);
        $vehicle->setDailyRate($dailyRate);

        $this->entityManager->persist($vehicle);
        $this->entityManager->flush();
    }
}