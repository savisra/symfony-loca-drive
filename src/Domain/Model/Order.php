<?php

namespace App\Domain\Model;

use App\Domain\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Model\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private ?OrderStatus $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Rental::class, orphanRemoval: true)]
    private Collection $rentals;

    public function __construct(
        User $customer,
    )
    {
        $this->customer = $customer;
        $this->createdAt = new DateTimeImmutable();
        $this->setStatus(OrderStatus::CART);
        $this->rentals = new ArrayCollection();
        $this->determinePrice();
    }

    public function determinePrice()
    {
        $rentals = $this->getRentals();
        $price = 0;

        foreach ($rentals as $rental) {
            $price += $rental->getPrice();
        }

        $this->totalPrice = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setOrder($this);
            $this->determinePrice();
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getOrder() === $this) {
                $rental->setOrder(null);
                $this->determinePrice();
            }
        }

        return $this;
    }
}
