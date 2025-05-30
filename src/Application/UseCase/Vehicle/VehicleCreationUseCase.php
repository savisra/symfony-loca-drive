<?php

namespace App\Application\UseCase\Vehicle;

use App\Domain\Model\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class VehicleCreationUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        string $model, 
        string $brand, 
        float $dailyRate
    ): int {
        $existingCar = $this->entityManager->getRepository(Vehicle::class)->findOneBy(['model' => $model, 'brand' => $brand]);

        if ($existingCar) {
            throw new Exception('A vehicle with the same brand and the same model already exists.');
        }

        $vehicle = new Vehicle(
            $model,
            $brand,
            $dailyRate
        );

        $this->entityManager->persist($vehicle);
        $this->entityManager->flush();

        return $vehicle->getId();
    }
}