<?php

namespace App\Domain\Repository;

use App\Domain\Model\Vehicle;

interface VehicleRepositoryInterface
{
    public function findBy(string $id): ?Vehicle;
    public function findAll(): array;
}