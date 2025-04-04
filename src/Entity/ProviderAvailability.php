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

use App\Repository\ProviderAvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderAvailabilityRepository::class)]
#[ORM\Table(name: 'provider_availabilities')]
#[ORM\UniqueConstraint(name: 'UNIQ_PROVIDER_SCHEDULE', columns: ['provider_id', 'day_of_week'])]
class ProviderAvailability extends BaseEntity
{
    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'availabilities')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], message: 'Invalid day of week')]
    #[Groups(['availability_detail', 'availability_list'])]
    private string $dayOfWeek;

    #[ORM\Column(type: 'time', nullable: true)]
    #[Groups(['availability_detail'])]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'time', nullable: true)]
    #[Groups(['availability_detail'])]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isAvailable = false;

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

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

    public function isValidTimeSlot(): bool
    {
        return $this->isAvailable
            && null !== $this->startTime
            && null !== $this->endTime
            && $this->startTime < $this->endTime;
    }
}
