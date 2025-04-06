<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'app_auth_')]
class AuthenticationController extends AbstractController
{
    private const JWT_COOKIE_NAME = 'JWT_TOKEN';
    private const REFRESH_COOKIE_NAME = 'REFRESH_TOKEN';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager,
    ) {
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $tokenStorage->setToken(null);
        $response = $this->json(['message' => 'Logged out successfully']);
        $response->headers->clearCookie(self::JWT_COOKIE_NAME);
        $response->headers->clearCookie(self::REFRESH_COOKIE_NAME);

        return $response;
    }

    #[Route('/token/refresh', name: 'token_refresh', methods: ['POST'])]
    public function refreshToken(Request $request, UserRepository $userRepository): JsonResponse
    {
        $refreshToken = $request->cookies->get(self::REFRESH_COOKIE_NAME);

        if (!$refreshToken) {
            return $this->json([
                'error' => 'Refresh token is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $payload = $this->jwtManager->parse($refreshToken);
            $user = $userRepository->find($payload['sub'] ?? null);

            if (!$user) {
                return $this->json([
                    'error' => 'User not found',
                ], Response::HTTP_UNAUTHORIZED);
            }


            $accessToken = $this->jwtManager->create($user);
            $newRefreshToken = $this->generateRefreshToken($user);
            $response = $this->json([
                'message' => 'Token refreshed successfully',
            ]);

            $response->headers->setCookie(
                Cookie::create(self::JWT_COOKIE_NAME)
                    ->withValue($accessToken)
                    ->withHttpOnly(true)
                    ->withSecure(true)
                    ->withSameSite('strict')
            );

            $response->headers->setCookie(
                Cookie::create(self::REFRESH_COOKIE_NAME)
                    ->withValue($newRefreshToken)
                    ->withHttpOnly(true)
                    ->withSecure(true)
                    ->withSameSite('strict')
            );

            return $response;
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Invalid refresh token',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/check', name: 'check', methods: ['GET'])]
    public function checkAuthentication(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'authenticated' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/change-password', name: 'change_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        if (!$user) {
            return $this->json([
                'error' => 'Authentication required',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!$this->passwordHasher->isPasswordValid($user, $data['current_password'] ?? '')) {
            return $this->json([
                'error' => 'Current password is incorrect',
            ], Response::HTTP_BAD_REQUEST);
        }

        $newHashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['new_password'] ?? ''
        );

        $user->setPassword($newHashedPassword);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Password changed successfully',
        ]);
    }

    private function generateRefreshToken(User $user): string
    {
        $payload = [
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'type' => 'refresh',
            'iat' => time(),
            'exp' => time() + (30 * 24 * 60 * 60),
            'jti' => bin2hex(random_bytes(16)),
        ];

        return $this->jwtManager->create($user, $payload);
    }
}