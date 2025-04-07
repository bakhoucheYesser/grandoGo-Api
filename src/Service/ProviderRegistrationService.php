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

namespace App\Service;

use App\Entity\Provider;
use App\Entity\ProviderVehicle;
use App\Entity\User;
use App\Entity\VehicleType;
use App\Repository\VehicleTypeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Service handling provider registration process.
 */
class ProviderRegistrationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly VehicleTypeRepository $vehicleTypeRepository,
        private readonly string $adminEmail,
    ) {
    }

    /**
     * Register a new provider with their vehicle information.
     *
     * @param array $userData User personal information
     * @param array $providerData Provider specific information
     * @param array $vehicleData Vehicle information
     *
     * @return Provider The newly created provider entity
     *
     * @throws BadRequestException If registration fails
     */
    public function registerProvider(array $userData, array $providerData, array $vehicleData): Provider
    {
        try {
            $this->entityManager->beginTransaction();

            // Create and persist user
            $user = $this->createUser($userData);
            $this->entityManager->persist($user);

            // Create and persist provider
            $provider = $this->createProvider($user, $providerData);
            $this->entityManager->persist($provider);

            // Create and persist vehicle
            $vehicle = $this->createVehicle($provider, $vehicleData);
            $this->entityManager->persist($vehicle);

            $this->entityManager->flush();
            $this->entityManager->commit();

            // Send notification email to admin
//            $this->notifyAdmin($provider);

            return $provider;
        } catch (\Throwable $e) {
            // Check if a transaction is active before attempting to roll it back
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->rollback();
            }

            $this->logger->error('Provider registration failed: ' . $e->getMessage(), [
                'exception' => $e,
                'userData' => $userData,
            ]);

            throw new BadRequestException('Provider registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a new user entity from registration data.
     */
    private function createUser(array $userData): User
    {
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $userData['email']]);

        if ($existingUser) {
            throw new \DomainException('Email is already registered');
        }

        $user = new User();
        $user->setEmail($userData['email']);
        $user->setFirstName($userData['firstName']);
        $user->setLastName($userData['lastName']);
        $user->setPhoneNumber($userData['phoneNumber']);
        $user->setUserType(User::TYPE_PROVIDER);
        $user->setStatus(User::STATUS_PENDING);

        // Generate verification token
        $user->setVerificationToken(Uuid::v4()->toRfc4122());

        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $userData['password']
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    /**
     * Create a new provider entity from registration data.
     */
    private function createProvider(User $user, array $providerData): Provider
    {
        $provider = new Provider();
        $provider->setUser($user);
        $provider->setCompanyName($providerData['companyName'] ?? null);
        $provider->setBusinessLicense($providerData['businessLicense'] ?? null);
        $provider->setTaxId($providerData['taxId'] ?? null);
        $provider->setServiceAreaRadius($providerData['serviceAreaRadius'] ?? 30);
        $provider->setVerificationStatus(Provider::VERIFICATION_PENDING);

        return $provider;
    }

    /**
     * Create a new vehicle entity from registration data.
     */
    private function createVehicle(Provider $provider, array $vehicleData): ProviderVehicle
    {
        $vehicle = new ProviderVehicle();
        $vehicle->setProvider($provider);
        $vehicle->setMake($vehicleData['make']);
        $vehicle->setModel($vehicleData['model']);
        $vehicle->setYear($vehicleData['year']);
        $vehicle->setColor($vehicleData['color'] ?? null);
        $vehicle->setType($vehicleData['type']);
        $vehicle->setLicensePlate($vehicleData['licensePlate']);
        $vehicle->setVerified(false);

        // Associate with vehicle type if provided
        if (!empty($vehicleData['vehicleTypeId'])) {
            $vehicleType = $this->vehicleTypeRepository->find($vehicleData['vehicleTypeId']);
            if ($vehicleType instanceof VehicleType) {
                $vehicle->setVehicleType($vehicleType);
            }
        }

        return $vehicle;
    }

    /**
     * Send notification email to admin about new provider registration.
     */
    private function notifyAdmin(Provider $provider): void
    {
        $user = $provider->getUser();

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@grandogo.com', 'GrandoGo'))
            ->to($this->adminEmail)
            ->subject('New Provider Registration - Verification Required')
            ->htmlTemplate('emails/admin/provider_registration.html.twig')
            ->context([
                'provider' => $provider,
                'user' => $user,
                'registrationDate' => new DateTime(),
            ]);

        $this->mailer->send($email);

        $this->logger->info('Provider registration notification sent to admin', [
            'providerId' => $provider->getId(),
            'email' => $user->getEmail(),
        ]);
    }
}