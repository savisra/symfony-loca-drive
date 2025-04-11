<?php

namespace App\Domain\Model;

use App\Domain\Model\Order;
use App\Domain\Model\Vehicle;
use App\Domain\Repository\RentalRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Insurance $insurance = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    private ?string $pickupLocation = null;

    #[ORM\Column]
    private ?bool $hasInsurance = null;

    #[ORM\Column]
    private ?float $price = null;

    public function __construct(
        Vehicle $vehicle,
        Order $order,
        ?Insurance $insurance = null,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $pickupLocation,
    ) 
    {
        $this->ensureDatesCoherence($startDate, $endDate);
        
        $this->vehicle = $vehicle;
        $this->order = $order;
        $this->insurance = $insurance;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->pickupLocation = $pickupLocation;
        $this->hasInsurance = !empty($insurance);
        
        $this->determinePrice();
    }

    private function ensureDatesCoherence(DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        $today = new DateTimeImmutable();

        if ($startDate <= $today) {
            throw new Exception('Start date cannot be <= today\'s date.');
        }
        if ($startDate >= $endDate) {
            throw new Exception('End date cannot be <= start date.');
        }
    }

    private function determinePrice()
    {
        $dailyRate = $this->vehicle->getDailyRate();
        $duration = $this->startDate->diff($this->endDate)->days;
        $price = $dailyRate * $duration;

        if ($this->hasInsurance()) {
            $price += $this->insurance->getPrice();
        }

        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->$vehicle = $vehicle;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getInsurance(): ?Insurance
    {
        return $this->insurance;
    }

    public function setInsurance(?Insurance $insurance): static
    {
        $this->insurance = $insurance;
        $this->hasInsurance = true;
        $this->determinePrice();
        $this->order->determinePrice();

        return $this;
    }

    public function clearInsurance(): static
    {
        $this->insurance = null;
        $this->hasInsurance = false;
        $this->determinePrice();
        $this->order->determinePrice();
        
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPickupLocation(): ?string
    {
        return $this->pickupLocation;
    }

    public function setPickupLocation(string $pickupLocation): static
    {
        $this->pickupLocation = $pickupLocation;

        return $this;
    }

    public function hasInsurance(): ?bool
    {
        return $this->hasInsurance;
    }

    public function setHasInsurance(bool $hasInsurance): static
    {
        $this->hasInsurance = $hasInsurance;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
