<?php
/*
 * @author Yesser Bkhouch <yesserbakhouch@hotmail.com>
 */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_USERS_EMAIL', columns: ['email'])]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const TYPE_CUSTOMER = 'customer';
    public const TYPE_PROVIDER = 'provider';
    public const TYPE_ADMIN = 'admin';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_PENDING = 'pending_verification';

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['default', 'profile'])]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[+]?[0-9]{10,15}$/')]
    #[Groups(['profile'])]
    private string $phoneNumber;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Groups(['default', 'profile'])]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Groups(['default', 'profile'])]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['profile'])]
    private ?string $profileImage = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_CUSTOMER, self::TYPE_PROVIDER, self::TYPE_ADMIN])]
    #[Groups(['admin'])]
    private string $userType;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(choices: [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_SUSPENDED, self::STATUS_PENDING])]
    #[Groups(['admin'])]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $verificationToken = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $passwordResetExpiresAt = null;

    #[ORM\Column(type: 'json')]
    private array $notificationPreferences = [
        'email' => true,
        'sms' => true,
        'push' => true,
    ];

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Customer::class, cascade: ['persist', 'remove'])]
    private ?Customer $customer = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Provider::class, cascade: ['persist', 'remove'])]
    private ?Provider $provider = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if (self::TYPE_PROVIDER === $this->userType) {
            $roles[] = 'ROLE_PROVIDER';
        } elseif (self::TYPE_ADMIN === $this->userType) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

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

    public function isActive(): bool
    {
        return self::STATUS_ACTIVE === $this->status;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function updateLastLogin(): self
    {
        $this->lastLoginAt = new \DateTime();

        return $this;
    }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): self
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    public function getPasswordResetExpiresAt(): ?\DateTimeInterface
    {
        return $this->passwordResetExpiresAt;
    }

    public function setPasswordResetExpiresAt(?\DateTimeInterface $passwordResetExpiresAt): self
    {
        $this->passwordResetExpiresAt = $passwordResetExpiresAt;

        return $this;
    }

    public function getNotificationPreferences(): array
    {
        return $this->notificationPreferences;
    }

    public function setNotificationPreferences(array $notificationPreferences): self
    {
        $this->notificationPreferences = $notificationPreferences;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        // unset the owning side of the relation if necessary
        if (null === $customer && null !== $this->customer) {
            $this->customer->setUser(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $customer && $customer->getUser() !== $this) {
            $customer->setUser($this);
        }

        $this->customer = $customer;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        // unset the owning side of the relation if necessary
        if (null === $provider && null !== $this->provider) {
            $this->provider->setUser(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $provider && $provider->getUser() !== $this) {
            $provider->setUser($this);
        }

        $this->provider = $provider;

        return $this;
    }

    public function isCustomer(): bool
    {
        return self::TYPE_CUSTOMER === $this->userType;
    }

    public function isProvider(): bool
    {
        return self::TYPE_PROVIDER === $this->userType;
    }

    public function isAdmin(): bool
    {
        return self::TYPE_ADMIN === $this->userType;
    }
}
