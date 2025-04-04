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

use App\Repository\BookingItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingItemRepository::class)]
#[ORM\Table(name: 'booking_items')]
class BookingItem extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Booking::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'id', nullable: false)]
    private ?Booking $booking = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['booking_item', 'booking_detail'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $category = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive]
    #[Groups(['booking_item', 'booking_detail'])]
    private int $quantity = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $weight = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $weightUnit = 'lbs';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $length = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $width = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $height = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $dimensionUnit = 'in';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['booking_item', 'booking_detail'])]
    private bool $requiresSpecialHandling = false;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $specialHandlingInstructions = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['booking_item', 'booking_detail'])]
    private bool $isFragile = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['booking_item', 'booking_detail'])]
    private ?string $imageUrl = null;

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWeightUnit(): ?string
    {
        return $this->weightUnit;
    }

    public function setWeightUnit(?string $weightUnit): self
    {
        $this->weightUnit = $weightUnit;

        return $this;
    }

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getDimensionUnit(): ?string
    {
        return $this->dimensionUnit;
    }

    public function setDimensionUnit(?string $dimensionUnit): self
    {
        $this->dimensionUnit = $dimensionUnit;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isRequiresSpecialHandling(): bool
    {
        return $this->requiresSpecialHandling;
    }

    public function setRequiresSpecialHandling(bool $requiresSpecialHandling): self
    {
        $this->requiresSpecialHandling = $requiresSpecialHandling;

        return $this;
    }

    public function getSpecialHandlingInstructions(): ?string
    {
        return $this->specialHandlingInstructions;
    }

    public function setSpecialHandlingInstructions(?string $specialHandlingInstructions): self
    {
        $this->specialHandlingInstructions = $specialHandlingInstructions;

        return $this;
    }

    public function isFragile(): bool
    {
        return $this->isFragile;
    }

    public function setIsFragile(bool $isFragile): self
    {
        $this->isFragile = $isFragile;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getFormattedDimensions(): ?string
    {
        if (null === $this->length || null === $this->width || null === $this->height) {
            return null;
        }

        return "{$this->length} × {$this->width} × {$this->height} {$this->dimensionUnit}";
    }

    public function getFormattedWeight(): ?string
    {
        if (null === $this->weight) {
            return null;
        }

        return "{$this->weight} {$this->weightUnit}";
    }

    public function getVolume(): ?float
    {
        if (null === $this->length || null === $this->width || null === $this->height) {
            return null;
        }

        return (float) $this->length * (float) $this->width * (float) $this->height;
    }

    public function getVolumeInCubicFeet(): ?float
    {
        $volume = $this->getVolume();
        if (null === $volume) {
            return null;
        }

        // Convert to cubic feet if needed
        if ('in' === $this->dimensionUnit) {
            return $volume / 1728; // 12³ = 1728 cubic inches in a cubic foot
        }

        if ('cm' === $this->dimensionUnit) {
            return $volume / 28316.85; // 28,316.85 cubic cm in a cubic foot
        }

        if ('m' === $this->dimensionUnit) {
            return $volume * 35.3147; // 1 cubic meter = 35.3147 cubic feet
        }

        return $volume; // Assume it's already in cubic feet
    }

    public function getTotalWeight(): ?float
    {
        if (null === $this->weight) {
            return null;
        }

        return (float) $this->weight * $this->quantity;
    }
}
