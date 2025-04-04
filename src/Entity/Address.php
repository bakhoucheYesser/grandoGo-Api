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

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\Table(name: 'addresses')]
class Address extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: true)]
    private ?Customer $customer = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['address', 'address_list'])]
    #[Assert\Choice(choices: ['home', 'work', 'other'])]
    private ?string $addressType = 'home';

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['address', 'address_list'])]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['address', 'address_list'])]
    #[Assert\NotBlank]
    private string $streetAddress;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['address', 'address_list'])]
    private ?string $apartmentUnit = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['address', 'address_list'])]
    #[Assert\NotBlank]
    private string $city;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['address', 'address_list'])]
    #[Assert\NotBlank]
    private string $state;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['address', 'address_list'])]
    #[Assert\NotBlank]
    private string $postalCode;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['address', 'address_list'])]
    private string $country = 'USA';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8)]
    #[Assert\NotBlank]
    private string $latitude;

    #[ORM\Column(type: 'decimal', precision: 11, scale: 8)]
    #[Assert\NotBlank]
    private string $longitude;

    #[ORM\Column(type: 'boolean')]
    private bool $isDefault = false;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['address'])]
    private ?string $deliveryInstructions = null;

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getAddressType(): ?string
    {
        return $this->addressType;
    }

    public function setAddressType(?string $addressType): self
    {
        $this->addressType = $addressType;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    public function setStreetAddress(string $streetAddress): self
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    public function getApartmentUnit(): ?string
    {
        return $this->apartmentUnit;
    }

    public function setApartmentUnit(?string $apartmentUnit): self
    {
        $this->apartmentUnit = $apartmentUnit;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getDeliveryInstructions(): ?string
    {
        return $this->deliveryInstructions;
    }

    public function setDeliveryInstructions(?string $deliveryInstructions): self
    {
        $this->deliveryInstructions = $deliveryInstructions;

        return $this;
    }

    public function getFullAddress(): string
    {
        $address = $this->streetAddress;

        if ($this->apartmentUnit) {
            $address .= ', '.$this->apartmentUnit;
        }

        $address .= ', '.$this->city.', '.$this->state.' '.$this->postalCode;

        if ($this->country && 'USA' != $this->country) {
            $address .= ', '.$this->country;
        }

        return $address;
    }

    public function getDisplayName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->addressType) {
            return ucfirst($this->addressType);
        }

        return $this->getFullAddress();
    }
}
