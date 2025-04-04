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

use App\Repository\ProviderVehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderVehicleRepository::class)]
#[ORM\Table(name: 'provider_vehicles')]
class ProviderVehicle extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'vehicles')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Vehicle make is required')]
    #[Groups(['vehicle_detail', 'vehicle_list'])]
    private string $make;

    #[ORM\ManyToOne(targetEntity: VehicleType::class, inversedBy: 'vehicles')]
    #[ORM\JoinColumn(name: 'vehicle_type_id', referencedColumnName: 'id', nullable: true)]
    private ?VehicleType $vehicleType = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Vehicle model is required')]
    #[Groups(['vehicle_detail', 'vehicle_list'])]
    private string $model;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Vehicle year is required')]
    #[Groups(['vehicle_detail', 'vehicle_list'])]
    private string $year;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['vehicle_detail'])]
    private ?string $color = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Vehicle type is required')]
    #[Assert\Choice(choices: ['sedan', 'suv', 'truck', 'van', 'hybrid', 'electric'], message: 'Invalid vehicle type')]
    #[Groups(['vehicle_detail', 'vehicle_list'])]
    private string $type;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Assert\NotBlank(message: 'License plate is required')]
    #[Groups(['vehicle_detail'])]
    private string $licensePlate;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['vehicle_detail', 'vehicle_list'])]
    private bool $available = true;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['vehicle_detail'])]
    private bool $verified = false;

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getMake(): string
    {
        return $this->make;
    }

    public function setMake(string $make): self
    {
        $this->make = $make;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s %s', $this->year, $this->make, $this->model);
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
}
