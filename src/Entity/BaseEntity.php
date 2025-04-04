<?php
/*
 * @author Yesser Bkhouch <yesserbakhouch@hotmail.com>
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * Base entity that provides common fields for all entities.
 */
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntity
{
    /**
     * Primary key identifier.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    #[Groups(['default', 'list', 'detail'])]
    protected ?int $id = null;

    /**
     * Record creation timestamp.
     */
    #[ORM\Column(type: 'datetime')]
    #[Groups(['detail'])]
    protected ?\DateTimeInterface $createdAt = null;

    /**
     * Last update timestamp.
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['detail'])]
    protected ?\DateTimeInterface $updatedAt = null;

    /**
     * Active status flag.
     */
    #[ORM\Column(type: 'boolean')]
    #[Groups(['detail', 'admin'])]
    protected bool $isActive = true;

    /**
     * Version number for optimistic locking.
     */
    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    protected int $version = 1;

    /**
     * Optional deletion timestamp (for soft deletes).
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $deletedAt = null;

    /**
     * UUID for external references.
     */
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['default', 'list', 'detail'])]
    protected ?string $uuid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
        if (null === $this->uuid) {
            $this->uuid = Uuid::v4()->toRfc4122();
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }

    public function delete(): self
    {
        $this->deletedAt = new \DateTime();
        $this->isActive = false;

        return $this;
    }

    public function restore(): self
    {
        $this->deletedAt = null;
        $this->isActive = true;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
