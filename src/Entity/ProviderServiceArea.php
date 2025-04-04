<?php

namespace App\Entity;

use App\Repository\ProviderServiceAreaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderServiceAreaRepository::class)]
#[ORM\Table(name: 'provider_service_areas')]
class ProviderServiceArea extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'serviceAreas')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Service area name is required')]
    #[Groups(['service_area_detail', 'service_area_list'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['service_area_detail'])]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['service_area_detail'])]
    private ?array $boundaries = null;


    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getBoundaries(): ?array
    {
        return $this->boundaries;
    }

    public function setBoundaries(?array $boundaries): self
    {
        $this->boundaries = $boundaries;
        return $this;
    }


}