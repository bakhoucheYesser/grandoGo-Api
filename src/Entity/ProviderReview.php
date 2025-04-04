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

use App\Repository\ProviderReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderReviewRepository::class)]
#[ORM\Table(name: 'provider_reviews')]
#[ORM\UniqueConstraint(name: 'UNIQ_REVIEW_BOOKING', columns: ['booking_id', 'customer_id', 'provider_id'])]
class ProviderReview extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private ?Provider $provider = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Booking::class)]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'id', nullable: false)]
    private ?Booking $booking = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    #[Groups(['review', 'review_list', 'provider_detail'])]
    private int $rating;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['review', 'review_list', 'provider_detail'])]
    private ?string $reviewText = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['review', 'provider_detail'])]
    private ?string $responseText = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['review', 'provider_detail'])]
    private ?\DateTimeInterface $responseDate = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isFlagged = false;

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
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

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewText(): ?string
    {
        return $this->reviewText;
    }

    public function setReviewText(?string $reviewText): self
    {
        $this->reviewText = $reviewText;

        return $this;
    }

    public function getResponseText(): ?string
    {
        return $this->responseText;
    }

    public function setResponseText(?string $responseText): self
    {
        $this->responseText = $responseText;

        if (null !== $responseText) {
            $this->responseDate = new \DateTime();
        }

        return $this;
    }

    public function getResponseDate(): ?\DateTimeInterface
    {
        return $this->responseDate;
    }

    public function setResponseDate(?\DateTimeInterface $responseDate): self
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    public function isFlagged(): bool
    {
        return $this->isFlagged;
    }

    public function setIsFlagged(bool $isFlagged): self
    {
        $this->isFlagged = $isFlagged;

        return $this;
    }

    public function flag(): self
    {
        $this->isFlagged = true;

        return $this;
    }

    public function unflag(): self
    {
        $this->isFlagged = false;

        return $this;
    }

    public function hasResponse(): bool
    {
        return null !== $this->responseText;
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function updateProviderRating(): void
    {
        if ($this->provider) {
            $this->provider->recalculateRating();
        }
    }
}
