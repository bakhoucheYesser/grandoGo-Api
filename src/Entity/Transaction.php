<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: 'transactions')]
class Transaction extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Booking::class)]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'id', nullable: true)]
    private ?Booking $booking = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Transaction type is required')]
    #[Assert\Choice(choices: ['payment', 'refund', 'credit', 'debit'], message: 'Invalid transaction type')]
    #[Groups(['transaction_detail', 'transaction_list'])]
    private string $type;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Amount is required')]
    #[Assert\Positive(message: 'Amount must be a positive number')]
    #[Groups(['transaction_detail', 'transaction_list'])]
    private float $amount;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Payment method is required')]
    #[Groups(['transaction_detail'])]
    private string $paymentMethod;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['transaction_detail'])]
    private ?string $stripeTransactionId = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Transaction status is required')]
    #[Assert\Choice(choices: ['pending', 'completed', 'failed', 'refunded'], message: 'Invalid transaction status')]
    #[Groups(['transaction_detail', 'transaction_list'])]
    private string $status = 'pending';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['transaction_detail'])]
    private ?string $notes = null;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getStripeTransactionId(): ?string
    {
        return $this->stripeTransactionId;
    }

    public function setStripeTransactionId(?string $stripeTransactionId): self
    {
        $this->stripeTransactionId = $stripeTransactionId;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }
}