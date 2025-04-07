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

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
class Provider extends BaseEntity
{
    public const VERIFICATION_PENDING = 'pending';
    public const VERIFICATION_VERIFIED = 'verified';
    public const VERIFICATION_REJECTED = 'rejected';

    #[ORM\OneToOne(inversedBy: 'provider', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['provider', 'provider_list'])]
    private ?string $companyName = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $businessLicense = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $taxId = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $stripeConnectId = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private string $commissionRate = '20.00';

    #[ORM\Column(type: 'integer')]
    private int $serviceAreaRadius = 30;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['provider_list'])]
    private bool $isAvailable = false;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8, nullable: true)]
    private ?string $currentLocationLat = null;

    #[ORM\Column(type: 'decimal', precision: 11, scale: 8, nullable: true)]
    private ?string $currentLocationLng = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $locationUpdatedAt = null;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, nullable: true)]
    #[Groups(['provider', 'provider_list'])]
    private ?string $rating = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['provider', 'provider_list'])]
    private int $reviewCount = 0;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $acceptanceRate = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $completionRate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $accountBalance = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalEarnings = '0.00';

    #[ORM\Column(type: 'string', length: 20)]
    private string $verificationStatus = self::VERIFICATION_PENDING;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'account_manager_id', referencedColumnName: 'id', nullable: true)]
    private ?User $accountManager = null;

    #[ORM\OneToMany(targetEntity: ProviderVehicle::class, mappedBy: 'provider', orphanRemoval: true)]
    private Collection $vehicles;

    #[ORM\OneToMany(targetEntity: ProviderAvailability::class, mappedBy: 'provider', orphanRemoval: true)]
    private Collection $availabilitySchedule;

    #[ORM\OneToMany(targetEntity: ProviderServiceArea::class, mappedBy: 'provider', orphanRemoval: true)]
    private Collection $serviceAreas;

    #[ORM\OneToMany(targetEntity: ProviderReview::class, mappedBy: 'provider')]
    private Collection $reviews;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'provider')]
    private Collection $bookings;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'provider')]
    private Collection $transactions;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
        $this->availabilitySchedule = new ArrayCollection();
        $this->serviceAreas = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getDisplayName(): string
    {
        if ($this->companyName) {
            return $this->companyName;
        }

        return $this->user ? $this->user->getFullName() : 'Provider';
    }

    public function getBusinessLicense(): ?string
    {
        return $this->businessLicense;
    }

    public function setBusinessLicense(?string $businessLicense): self
    {
        $this->businessLicense = $businessLicense;

        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): self
    {
        $this->taxId = $taxId;

        return $this;
    }

    public function getStripeConnectId(): ?string
    {
        return $this->stripeConnectId;
    }

    public function setStripeConnectId(?string $stripeConnectId): self
    {
        $this->stripeConnectId = $stripeConnectId;

        return $this;
    }

    public function getCommissionRate(): string
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(string $commissionRate): self
    {
        $this->commissionRate = $commissionRate;

        return $this;
    }

    public function getServiceAreaRadius(): int
    {
        return $this->serviceAreaRadius;
    }

    public function setServiceAreaRadius(int $serviceAreaRadius): self
    {
        $this->serviceAreaRadius = $serviceAreaRadius;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getCurrentLocationLat(): ?string
    {
        return $this->currentLocationLat;
    }

    public function setCurrentLocationLat(?string $currentLocationLat): self
    {
        $this->currentLocationLat = $currentLocationLat;

        return $this;
    }

    public function getCurrentLocationLng(): ?string
    {
        return $this->currentLocationLng;
    }

    public function setCurrentLocationLng(?string $currentLocationLng): self
    {
        $this->currentLocationLng = $currentLocationLng;

        return $this;
    }

    public function updateLocation(string $lat, string $lng): self
    {
        $this->currentLocationLat = $lat;
        $this->currentLocationLng = $lng;
        $this->locationUpdatedAt = new \DateTime();

        return $this;
    }

    public function getLocationUpdatedAt(): ?\DateTimeInterface
    {
        return $this->locationUpdatedAt;
    }

    public function setLocationUpdatedAt(?\DateTimeInterface $locationUpdatedAt): self
    {
        $this->locationUpdatedAt = $locationUpdatedAt;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewCount(): int
    {
        return $this->reviewCount;
    }

    public function setReviewCount(int $reviewCount): self
    {
        $this->reviewCount = $reviewCount;

        return $this;
    }

    public function incrementReviewCount(): self
    {
        ++$this->reviewCount;

        return $this;
    }

    public function getAcceptanceRate(): ?string
    {
        return $this->acceptanceRate;
    }

    public function setAcceptanceRate(?string $acceptanceRate): self
    {
        $this->acceptanceRate = $acceptanceRate;

        return $this;
    }

    public function getCompletionRate(): ?string
    {
        return $this->completionRate;
    }

    public function setCompletionRate(?string $completionRate): self
    {
        $this->completionRate = $completionRate;

        return $this;
    }

    public function getAccountBalance(): string
    {
        return $this->accountBalance;
    }

    public function setAccountBalance(string $accountBalance): self
    {
        $this->accountBalance = $accountBalance;

        return $this;
    }

    public function getTotalEarnings(): string
    {
        return $this->totalEarnings;
    }

    public function setTotalEarnings(string $totalEarnings): self
    {
        $this->totalEarnings = $totalEarnings;

        return $this;
    }

    public function getVerificationStatus(): string
    {
        return $this->verificationStatus;
    }

    public function setVerificationStatus(string $verificationStatus): self
    {
        $this->verificationStatus = $verificationStatus;

        return $this;
    }

    public function isVerified(): bool
    {
        return self::VERIFICATION_VERIFIED === $this->verificationStatus;
    }

    public function getAccountManager(): ?User
    {
        return $this->accountManager;
    }

    public function setAccountManager(?User $accountManager): self
    {
        $this->accountManager = $accountManager;

        return $this;
    }

    /**
     * @return Collection<int, ProviderVehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(ProviderVehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles[] = $vehicle;
            $vehicle->setProvider($this);
        }

        return $this;
    }

    public function removeVehicle(ProviderVehicle $vehicle): self
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getProvider() === $this) {
                $vehicle->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProviderAvailability>
     */
    public function getAvailabilitySchedule(): Collection
    {
        return $this->availabilitySchedule;
    }

    public function addAvailabilitySchedule(ProviderAvailability $schedule): self
    {
        if (!$this->availabilitySchedule->contains($schedule)) {
            $this->availabilitySchedule[] = $schedule;
            $schedule->setProvider($this);
        }

        return $this;
    }

    public function removeAvailabilitySchedule(ProviderAvailability $schedule): self
    {
        if ($this->availabilitySchedule->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getProvider() === $this) {
                $schedule->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProviderServiceArea>
     */
    public function getServiceAreas(): Collection
    {
        return $this->serviceAreas;
    }

    public function addServiceArea(ProviderServiceArea $serviceArea): self
    {
        if (!$this->serviceAreas->contains($serviceArea)) {
            $this->serviceAreas[] = $serviceArea;
            $serviceArea->setProvider($this);
        }

        return $this;
    }

    public function removeServiceArea(ProviderServiceArea $serviceArea): self
    {
        if ($this->serviceAreas->removeElement($serviceArea)) {
            // set the owning side to null (unless already changed)
            if ($serviceArea->getProvider() === $this) {
                $serviceArea->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProviderReview>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(ProviderReview $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProvider($this);
            $this->incrementReviewCount();
            $this->recalculateRating();
        }

        return $this;
    }

    public function removeReview(ProviderReview $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProvider() === $this) {
                $review->setProvider(null);
            }
            $this->recalculateRating();
        }

        return $this;
    }

    public function recalculateRating(): void
    {
        $totalRating = 0;
        $count = 0;

        foreach ($this->reviews as $review) {
            $totalRating += $review->getRating();
            ++$count;
        }

        if ($count > 0) {
            $this->rating = (string) ($totalRating / $count);
            $this->reviewCount = $count;
        } else {
            $this->rating = null;
            $this->reviewCount = 0;
        }
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setProvider($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getProvider() === $this) {
                $booking->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setProvider($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getProvider() === $this) {
                $transaction->setProvider(null);
            }
        }

        return $this;
    }
}
