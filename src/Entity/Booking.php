<?php

/*
 * This file is part of the GrandoGo project.
 *
 * (c) Yesser Bkhouch <yesserbakhouch@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * @author Yesser Bkhouch <yesserbakhouch@hotmail.com>
 */

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'bookings')]
#[ORM\HasLifecycleCallbacks]
class Booking extends BaseEntity
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_EN_ROUTE = 'en_route';
    public const STATUS_ARRIVED = 'arrived';
    public const STATUS_LOADING = 'loading';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_UNLOADING = 'unloading';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['booking', 'booking_detail'])]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['booking', 'booking_detail', 'customer_booking'])]
    private ?Provider $provider = null;

    #[ORM\ManyToOne(targetEntity: ProviderVehicle::class)]
    #[ORM\JoinColumn(name: 'vehicle_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(['booking_detail', 'customer_booking'])]
    private ?ProviderVehicle $vehicle = null;

    #[ORM\ManyToOne(targetEntity: VehicleType::class)]
    #[ORM\JoinColumn(name: 'vehicle_type_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['booking', 'booking_detail'])]
    private ?VehicleType $vehicleType = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['booking', 'booking_detail'])]
    private \DateTimeInterface $scheduledTime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['booking_detail'])]
    private ?\DateTimeInterface $actualPickupTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['booking_detail'])]
    private ?\DateTimeInterface $actualDeliveryTime = null;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'pickup_address_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['booking', 'booking_detail'])]
    private ?Address $pickupAddress = null;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'delivery_address_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['booking', 'booking_detail'])]
    private ?Address $deliveryAddress = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking', 'booking_detail'])]
    private ?string $itemsDescription = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['booking', 'booking_detail'])]
    private string $distance;

    #[ORM\Column(type: 'integer')]
    #[Groups(['booking', 'booking_detail'])]
    private int $estimatedDuration;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['booking', 'booking_detail'])]
    private string $baseFare;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['booking', 'booking_detail'])]
    private string $laborFee;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['booking', 'booking_detail'])]
    private string $mileageFee;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['booking', 'booking_detail'])]
    private string $totalPrice;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['booking', 'booking_detail'])]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cancellationReason = null;

    #[ORM\Column(type: 'boolean')]
    private bool $customerCancelled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $providerCancelled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $systemCancelled = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $paymentIntentId = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['booking_detail'])]
    private ?string $paymentStatus = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isReviewed = false;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $commissionRate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $commissionAmount = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $providerPayout = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $routePolyline = null;

    #[ORM\OneToMany(mappedBy: 'booking', targetEntity: BookingStatusHistory::class, orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $statusHistory;

    #[ORM\OneToMany(mappedBy: 'booking', targetEntity: BookingItem::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $items;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $providerAlternatives = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCustomerSelected = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isPremiumSelection = false;

    public function __construct()
    {
        $this->statusHistory = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getVehicle(): ?ProviderVehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?ProviderVehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getVehicleType(): ?VehicleType
    {
        return $this->vehicleType;
    }

    public function setVehicleType(?VehicleType $vehicleType): self
    {
        $this->vehicleType = $vehicleType;

        return $this;
    }

    public function getScheduledTime(): \DateTimeInterface
    {
        return $this->scheduledTime;
    }

    public function setScheduledTime(\DateTimeInterface $scheduledTime): self
    {
        $this->scheduledTime = $scheduledTime;

        return $this;
    }

    public function getActualPickupTime(): ?\DateTimeInterface
    {
        return $this->actualPickupTime;
    }

    public function setActualPickupTime(?\DateTimeInterface $actualPickupTime): self
    {
        $this->actualPickupTime = $actualPickupTime;

        return $this;
    }

    public function getActualDeliveryTime(): ?\DateTimeInterface
    {
        return $this->actualDeliveryTime;
    }

    public function setActualDeliveryTime(?\DateTimeInterface $actualDeliveryTime): self
    {
        $this->actualDeliveryTime = $actualDeliveryTime;

        return $this;
    }

    public function getPickupAddress(): ?Address
    {
        return $this->pickupAddress;
    }

    public function setPickupAddress(?Address $pickupAddress): self
    {
        $this->pickupAddress = $pickupAddress;

        return $this;
    }

    public function getDeliveryAddress(): ?Address
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?Address $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getItemsDescription(): ?string
    {
        return $this->itemsDescription;
    }

    public function setItemsDescription(?string $itemsDescription): self
    {
        $this->itemsDescription = $itemsDescription;

        return $this;
    }

    public function getDistance(): string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getEstimatedDuration(): int
    {
        return $this->estimatedDuration;
    }

    public function setEstimatedDuration(int $estimatedDuration): self
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    public function getBaseFare(): string
    {
        return $this->baseFare;
    }

    public function setBaseFare(string $baseFare): self
    {
        $this->baseFare = $baseFare;

        return $this;
    }

    public function getLaborFee(): string
    {
        return $this->laborFee;
    }

    public function setLaborFee(string $laborFee): self
    {
        $this->laborFee = $laborFee;

        return $this;
    }

    public function getMileageFee(): string
    {
        return $this->mileageFee;
    }

    public function setMileageFee(string $mileageFee): self
    {
        $this->mileageFee = $mileageFee;

        return $this;
    }

    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status, string $notes = null): self
    {
        $oldStatus = $this->status;
        $this->status = $status;

        // Add an entry to the status history
        $historyEntry = new BookingStatusHistory();
        $historyEntry->setBooking($this);
        $historyEntry->setStatus($status);
        $historyEntry->setNotes($notes);
        $historyEntry->setPreviousStatus($oldStatus);
        $this->statusHistory->add($historyEntry);

        return $this;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): self
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }

    public function isCustomerCancelled(): bool
    {
        return $this->customerCancelled;
    }

    public function setCustomerCancelled(bool $customerCancelled): self
    {
        $this->customerCancelled = $customerCancelled;

        return $this;
    }

    public function isProviderCancelled(): bool
    {
        return $this->providerCancelled;
    }

    public function setProviderCancelled(bool $providerCancelled): self
    {
        $this->providerCancelled = $providerCancelled;

        return $this;
    }

    public function isSystemCancelled(): bool
    {
        return $this->systemCancelled;
    }

    public function setSystemCancelled(bool $systemCancelled): self
    {
        $this->systemCancelled = $systemCancelled;

        return $this;
    }

    public function getPaymentIntentId(): ?string
    {
        return $this->paymentIntentId;
    }

    public function setPaymentIntentId(?string $paymentIntentId): self
    {
        $this->paymentIntentId = $paymentIntentId;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function isReviewed(): bool
    {
        return $this->isReviewed;
    }

    public function setIsReviewed(bool $isReviewed): self
    {
        $this->isReviewed = $isReviewed;

        return $this;
    }

    public function getCommissionRate(): ?string
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(?string $commissionRate): self
    {
        $this->commissionRate = $commissionRate;

        return $this;
    }

    public function getCommissionAmount(): ?string
    {
        return $this->commissionAmount;
    }

    public function setCommissionAmount(?string $commissionAmount): self
    {
        $this->commissionAmount = $commissionAmount;

        return $this;
    }

    public function getProviderPayout(): ?string
    {
        return $this->providerPayout;
    }

    public function setProviderPayout(?string $providerPayout): self
    {
        $this->providerPayout = $providerPayout;

        return $this;
    }

    public function getRoutePolyline(): ?array
    {
        return $this->routePolyline;
    }

    public function setRoutePolyline(?array $routePolyline): self
    {
        $this->routePolyline = $routePolyline;

        return $this;
    }

    /**
     * @return Collection<int, BookingStatusHistory>
     */
    public function getStatusHistory(): Collection
    {
        return $this->statusHistory;
    }

    public function getLatestStatusHistory(): ?BookingStatusHistory
    {
        if ($this->statusHistory->isEmpty()) {
            return null;
        }

        return $this->statusHistory->first();
    }

    /**
     * @return Collection<int, BookingItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(BookingItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setBooking($this);
        }

        return $this;
    }

    public function removeItem(BookingItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getBooking() === $this) {
                $item->setBooking(null);
            }
        }

        return $this;
    }

    public function getProviderAlternatives(): ?array
    {
        return $this->providerAlternatives;
    }

    public function setProviderAlternatives(?array $providerAlternatives): self
    {
        $this->providerAlternatives = $providerAlternatives;

        return $this;
    }

    public function isCustomerSelected(): bool
    {
        return $this->isCustomerSelected;
    }

    public function setIsCustomerSelected(bool $isCustomerSelected): self
    {
        $this->isCustomerSelected = $isCustomerSelected;

        return $this;
    }

    public function isPremiumSelection(): bool
    {
        return $this->isPremiumSelection;
    }

    public function setIsPremiumSelection(bool $isPremiumSelection): self
    {
        $this->isPremiumSelection = $isPremiumSelection;

        return $this;
    }

    /**
     * Cancel the booking with a reason.
     */
    public function cancel(string $reason, string $cancelledBy = 'system'): self
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancellationReason = $reason;

        switch ($cancelledBy) {
            case 'customer':
                $this->customerCancelled = true;
                break;
            case 'provider':
                $this->providerCancelled = true;
                break;
            default:
                $this->systemCancelled = true;
                break;
        }

        $this->setStatus(self::STATUS_CANCELLED, $reason);

        return $this;
    }

    /**
     * Complete the booking.
     */
    public function complete(): self
    {
        $this->status = self::STATUS_COMPLETED;
        $this->actualDeliveryTime = new \DateTime();
        $this->setStatus(self::STATUS_COMPLETED, 'Delivery completed');

        return $this;
    }

    /**
     * Calculate commission amount and provider payout.
     */
    public function calculateCommission(): self
    {
        if (null === $this->commissionRate) {
            return $this;
        }

        $totalPrice = (float) $this->totalPrice;
        $rate = (float) $this->commissionRate / 100;

        $commissionAmount = $totalPrice * $rate;
        $providerPayout = $totalPrice - $commissionAmount;

        $this->commissionAmount = (string) round($commissionAmount, 2);
        $this->providerPayout = (string) round($providerPayout, 2);

        return $this;
    }

    /**
     * Check if the booking is in an active state.
     */
    public function isActive(): bool
    {
        $activeStatuses = [
            self::STATUS_CONFIRMED,
            self::STATUS_ASSIGNED,
            self::STATUS_EN_ROUTE,
            self::STATUS_ARRIVED,
            self::STATUS_LOADING,
            self::STATUS_IN_TRANSIT,
            self::STATUS_UNLOADING,
        ];

        return \in_array($this->status, $activeStatuses, true);
    }

    /**
     * Check if the booking can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        $cancellableStatuses = [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_ASSIGNED,
        ];

        return \in_array($this->status, $cancellableStatuses, true);
    }

    /**
     * Set the price components from a price calculation.
     */
    public function setPriceComponents(array $priceComponents): self
    {
        $this->baseFare = (string) $priceComponents['baseFare'];
        $this->laborFee = (string) $priceComponents['laborFee'];
        $this->mileageFee = (string) $priceComponents['mileageFee'];
        $this->totalPrice = (string) $priceComponents['totalPrice'];

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        // Set initial status history entry
        $historyEntry = new BookingStatusHistory();
        $historyEntry->setBooking($this);
        $historyEntry->setStatus($this->status);
        $historyEntry->setNotes('Booking created');
        $this->statusHistory->add($historyEntry);

        // Calculate commission if not already set
        if (null !== $this->commissionRate && null === $this->commissionAmount) {
            $this->calculateCommission();
        }
    }
}
