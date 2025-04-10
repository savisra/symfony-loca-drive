<?php

namespace App\Application\UseCase\Vehicle;

use Doctrine\ORM\EntityManagerInterface;

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
        
    }
}