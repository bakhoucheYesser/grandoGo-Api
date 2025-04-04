<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'app_auth_')]
class AuthenticationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}



    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(TokenStorageInterface $tokenStorage): JsonResponse
    {
        // Clear the token storage
        $tokenStorage->setToken(null);

        // Create response with cleared cookies
        $response = $this->json(['message' => 'Logged out successfully']);

        // Clear authentication cookies
        $response->headers->clearCookie('JWT_TOKEN');
        $response->headers->clearCookie('REFRESH_TOKEN');

        return $response;
    }

    #[Route('/check', name: 'check', methods: ['GET'])]
    public function checkAuthentication(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'authenticated' => false
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles()
            ]
        ]);
    }

    #[Route('/token/refresh', name: 'token_refresh', methods: ['POST'])]
    public function refreshToken(Request $request, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $refreshToken = $data['refresh_token'] ?? null;

            if (!$refreshToken) {
                return $this->json([
                    'error' => 'Refresh token is required'
                ], Response::HTTP_BAD_REQUEST);
            }

            try {
                // Parse the refresh token
                $payload = $jwtManager->parse($refreshToken);

                // Find user
                $user = $userRepository->find($payload['sub'] ?? null);

                if (!$user) {
                    return $this->json([
                        'error' => 'User not found'
                    ], Response::HTTP_UNAUTHORIZED);
                }

                // Generate new tokens
                $accessToken = $jwtManager->create($user);
                $newRefreshToken = $jwtManager->create($user, [
                    'sub' => $user->getId(),
                    'type' => 'refresh',
                    'exp' => time() + (30 * 24 * 60 * 60) // 30 days
                ]);

                return $this->json([
                    'token' => $accessToken,
                    'refresh_token' => $newRefreshToken
                ]);

            } catch (\Exception $e) {
                return $this->json([
                    'error' => 'Invalid refresh token',
                    'message' => $e->getMessage()
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Unexpected error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/change-password', name: 'change_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        #[CurrentUser] ?User $user
    ): JsonResponse {
        if (!$user) {
            return $this->json([
                'error' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        // Verify current password
        if (!$this->passwordHasher->isPasswordValid($user, $data['current_password'] ?? '')) {
            return $this->json([
                'error' => 'Current password is incorrect'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Hash new password
        $newHashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['new_password'] ?? ''
        );

        // Update password
        $user->setPassword($newHashedPassword);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Password changed successfully'
        ]);
    }

}