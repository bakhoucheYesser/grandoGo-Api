<?php
/*
 * @author Yesser Bkhouch <yesserbakhouch@hotmail.com>
 */

namespace App\Entity;

/*
 * class Customer
 *
 * @author Yesser Bkhouch <yesserbakhouch@hotmail.com>
 */
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customers')]
class Customer extends BaseEntity
{
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'customer')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'default_address_id', referencedColumnName: 'id', nullable: true)]
    private ?Address $defaultAddress = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $savedCardToken = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $paymentMethodId = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $stripeCustomerId = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true, unique: true)]
    private ?string $referralCode = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'referred_by', referencedColumnName: 'id', nullable: true)]
    private ?Customer $referredBy = null;

    #[ORM\Column(type: 'integer')]
    private int $totalBookings = 0;

    #[ORM\Column(type: 'integer')]
    private int $completedBookings = 0;

    #[ORM\Column(type: 'integer')]
    private int $cancelledBookings = 0;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Address::class, orphanRemoval: true)]
    private Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: ProviderReview::class)]
    private Collection $reviews;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getDefaultAddress(): ?Address
    {
        return $this->defaultAddress;
    }

    public function setDefaultAddress(?Address $defaultAddress): self
    {
        $this->defaultAddress = $defaultAddress;

        return $this;
    }

    public function getSavedCardToken(): ?string
    {
        return $this->savedCardToken;
    }

    public function setSavedCardToken(?string $savedCardToken): self
    {
        $this->savedCardToken = $savedCardToken;

        return $this;
    }

    public function getPaymentMethodId(): ?string
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(?string $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }

    public function getReferralCode(): ?string
    {
        return $this->referralCode;
    }

    public function setReferralCode(?string $referralCode): self
    {
        $this->referralCode = $referralCode;

        return $this;
    }

    public function getReferredBy(): ?Customer
    {
        return $this->referredBy;
    }

    public function setReferredBy(?Customer $referredBy): self
    {
        $this->referredBy = $referredBy;

        return $this;
    }

    public function getTotalBookings(): int
    {
        return $this->totalBookings;
    }

    public function setTotalBookings(int $totalBookings): self
    {
        $this->totalBookings = $totalBookings;

        return $this;
    }

    public function incrementTotalBookings(): self
    {
        ++$this->totalBookings;

        return $this;
    }

    public function getCompletedBookings(): int
    {
        return $this->completedBookings;
    }

    public function setCompletedBookings(int $completedBookings): self
    {
        $this->completedBookings = $completedBookings;

        return $this;
    }

    public function incrementCompletedBookings(): self
    {
        ++$this->completedBookings;

        return $this;
    }

    public function getCancelledBookings(): int
    {
        return $this->cancelledBookings;
    }

    public function setCancelledBookings(int $cancelledBookings): self
    {
        $this->cancelledBookings = $cancelledBookings;

        return $this;
    }

    public function incrementCancelledBookings(): self
    {
        ++$this->cancelledBookings;

        return $this;
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
            $booking->setCustomer($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getCustomer() === $this) {
                $booking->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setCustomer($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCustomer() === $this) {
                $address->setCustomer(null);
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
            $review->setCustomer($this);
        }

        return $this;
    }

    public function removeReview(ProviderReview $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getCustomer() === $this) {
                $review->setCustomer(null);
            }
        }

        return $this;
    }
}
