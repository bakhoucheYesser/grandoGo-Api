<?php

namespace App\Entity;

use App\Repository\VehicleTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehicleTypeRepository::class)]
#[ORM\Table(name: 'vehicle_types')]
class VehicleType extends BaseEntity
{
    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Vehicle type name is required')]
    #[Groups(['vehicle_type_detail', 'vehicle_type_list'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['vehicle_type_detail'])]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Groups(['vehicle_type_detail'])]
    private ?float $baseRate = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['vehicle_type_detail', 'vehicle_type_list'])]
    private bool $enabled = true;

    #[ORM\Column(type: 'integer')]
    private int $passengerCapacity = 0;

    #[ORM\Column(type: 'integer')]
    private int $luggageCapacity = 0;

    #[ORM\OneToMany(mappedBy: 'vehicleType', targetEntity: ProviderVehicle::class)]
    private Collection $vehicles;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getBaseRate(): ?float
    {
        return $this->baseRate;
    }

    public function setBaseRate(?float $baseRate): self
    {
        $this->baseRate = $baseRate;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getPassengerCapacity(): int
    {
        return $this->passengerCapacity;
    }

    public function setPassengerCapacity(int $passengerCapacity): self
    {
        $this->passengerCapacity = $passengerCapacity;
        return $this;
    }

    public function getLuggageCapacity(): int
    {
        return $this->luggageCapacity;
    }

    public function setLuggageCapacity(int $luggageCapacity): self
    {
        $this->luggageCapacity = $luggageCapacity;
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
            $vehicle->setVehicleType($this);
        }

        return $this;
    }

    public function removeVehicle(ProviderVehicle $vehicle): self
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getVehicleType() === $this) {
                $vehicle->setVehicleType(null);
            }
        }

        return $this;
    }
}