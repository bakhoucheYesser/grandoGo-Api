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

namespace App\Controller\Api;

use App\Entity\Provider;
use App\Service\ProviderRegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * API Controller for provider registration.
 */
#[Route('/api/v1/registration', name: 'api_registration_')]
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly ProviderRegistrationService $registrationService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Register a new provider with vehicle information.
     */
    #[Route('/provider', name: 'provider', methods: ['POST'])]
    public function registerProvider(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['message' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        // Validate minimum required fields
        $requiredFields = [
            'user' => ['email', 'password', 'firstName', 'lastName', 'phoneNumber'],
            'vehicle' => ['make', 'model', 'year', 'type', 'licensePlate']
        ];

        foreach ($requiredFields as $section => $fields) {
            if (!isset($data[$section]) || !is_array($data[$section])) {
                return $this->json(['message' => "Missing '$section' section"], Response::HTTP_BAD_REQUEST);
            }

            foreach ($fields as $field) {
                if (empty($data[$section][$field])) {
                    return $this->json(['message' => "Missing required field: $section.$field"], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        try {
            $provider = $this->registrationService->registerProvider(
                $data['user'],
                $data['provider'] ?? [],
                $data['vehicle']
            );

            // Return serialized provider data
            return $this->json(
                [
                    'message' => 'Provider registration successful. Waiting for admin approval.',
                    'provider' => $provider,
                ],
                Response::HTTP_CREATED,
                [],
                [
                    AbstractNormalizer::GROUPS => ['default', 'profile'],
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                        return $object->getId();
                    },
                ]
            );
        } catch (\Exception $e) {
            return $this->json(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Check registration status for a provider
     */
    #[Route('/provider/status/{uuid}', name: 'provider_status', methods: ['GET'])]
    public function checkProviderStatus(string $uuid, EntityManagerInterface $entityManager): JsonResponse
    {
        // Find provider by UUID
        $provider = $entityManager->getRepository(Provider::class)
            ->findOneBy(['uuid' => $uuid]);

        if (!$provider) {
            return $this->json(['message' => 'Provider not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $provider->getUser();

        return $this->json([
            'verificationStatus' => $provider->getVerificationStatus(),
            'userStatus' => $user->getStatus(),
            'isPending' => ($provider->getVerificationStatus() === Provider::VERIFICATION_PENDING),
            'isVerified' => $provider->isVerified(),
            'message' => $this->getStatusMessage($provider),
        ]);
    }

    /**
     * Get user-friendly status message based on provider verification status
     */
    private function getStatusMessage(Provider $provider): string
    {
        return match ($provider->getVerificationStatus()) {
            Provider::VERIFICATION_PENDING => 'Your application is pending review by our team.',
            Provider::VERIFICATION_VERIFIED => 'Your account has been verified. You can now log in.',
            Provider::VERIFICATION_REJECTED => 'Your application has been rejected. Please contact support for details.',
            default => 'Unknown status.',
        };
    }
}