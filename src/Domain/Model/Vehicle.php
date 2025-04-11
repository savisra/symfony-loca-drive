<?php

namespace App\Domain\Model;

use App\Domain\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private ?float $dailyRate = null;

    public function __construct(
        string $model,
        string $brand,
        float $dailyRate
    )
    {
        $this->ensureRequiredFieldsAreProvided($model, $brand, $dailyRate);
        $this->ensureDailyRateIsPositive($dailyRate);

        $this->model = $model;
        $this->brand = $brand;
        $this->dailyRate = $dailyRate;
    }

    private function ensureRequiredFieldsAreProvided(string $model, string $brand, float $dailyRate)
    {
        if (!$model || !$brand || !$dailyRate) {
            throw new Exception("Model, brand, dailyRate are required fields. Passed: $model, $brand, $dailyRate");
        }
    }

    private function ensureDailyRateIsPositive(float $dailyRate)
    {
        if ($dailyRate <= 0) {
            throw new Exception("Daily rate must be > 0. Passed: $dailyRate");
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDailyRate(): ?float
    {
        return $this->dailyRate;
    }

    public function setDailyRate(float $dailyRate): static
    {
        $this->dailyRate = $dailyRate;

        return $this;
    }
}
