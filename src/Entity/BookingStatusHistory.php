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

use App\Repository\BookingStatusHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookingStatusHistoryRepository::class)]
#[ORM\Table(name: 'booking_status_history')]
class BookingStatusHistory extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Booking::class, inversedBy: 'statusHistory')]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'id', nullable: false)]
    private ?Booking $booking = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['booking_detail', 'status_history'])]
    private string $status;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['booking_detail', 'status_history'])]
    private ?string $previousStatus = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking_detail', 'status_history'])]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true)]
    #[Groups(['admin'])]
    private ?User $updatedBy = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['admin'])]
    private ?string $updatedVia = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPreviousStatus(): ?string
    {
        return $this->previousStatus;
    }

    public function setPreviousStatus(?string $previousStatus): self
    {
        $this->previousStatus = $previousStatus;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedVia(): ?string
    {
        return $this->updatedVia;
    }

    public function setUpdatedVia(?string $updatedVia): self
    {
        $this->updatedVia = $updatedVia;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function addMetadata(string $key, $value): self
    {
        if (null === $this->metadata) {
            $this->metadata = [];
        }

        $this->metadata[$key] = $value;

        return $this;
    }

    public function isTransitionTo(string $status): bool
    {
        return $this->status === $status;
    }

    public function isTransitionFrom(string $status): bool
    {
        return $this->previousStatus === $status;
    }

    public function getDisplayName(): string
    {
        return 'Status Change: '.($this->previousStatus ?? 'New').' → '.$this->status;
    }
}
