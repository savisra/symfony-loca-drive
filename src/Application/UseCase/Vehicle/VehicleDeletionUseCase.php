<?php

namespace App\Application\UseCase\Vehicle;

use App\Domain\Model\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class VehicleDeletionUseCase
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function execute(
        int $vehicleId
    ) {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($vehicleId);

        if (!$vehicle) {
            throw new Exception('No vehicle found with ID ' . $vehicleId);
        }

        $this->entityManager->remove($vehicle);
        $this->entityManager->flush();
    }
}