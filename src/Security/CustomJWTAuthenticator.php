<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomJWTAuthenticator extends AbstractAuthenticator
{
    private const JWT_COOKIE_NAME = 'JWT_TOKEN';
    private const REFRESH_COOKIE_NAME = 'REFRESH_TOKEN';

    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return \in_array($request->getPathInfo(), [
                '/api/login',
                '/api/token/refresh',
            ], true) && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $request->toArray();

        if ('/api/token/refresh' === $request->getPathInfo()) {
            return $this->handleRefreshTokenAuthentication($credentials);
        }

        return $this->handleLoginAuthentication($credentials);
    }

    private function handleLoginAuthentication(array $credentials): Passport
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            throw new AuthenticationException('Email and password are required');
        }

        return new Passport(
            new UserBadge($credentials['email'], function ($email) {
                $user = $this->userRepository->findOneBy(['email' => $email]);

                if (!$user) {
                    throw new UserNotFoundException("User with email $email not found");
                }

                return $user;
            }),
            new PasswordCredentials($credentials['password'])
        );
    }

    private function handleRefreshTokenAuthentication(array $credentials): Passport
    {

        $refreshToken = $credentials['refresh_token'] ??
            $this->getRefreshTokenFromCookie();

        if (!$refreshToken) {
            throw new AuthenticationException('Refresh token is required');
        }

        try {
            $payload = $this->jwtManager->parse($refreshToken);
            $user = $this->userRepository->find($payload['sub'] ?? null);

            if (!$user) {
                throw new UserNotFoundException('User not found');
            }

            return new Passport(
                new UserBadge($user->getEmail(), fn () => $user),
                new PasswordCredentials($user->getPassword())
            );
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid refresh token');
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();

        $accessToken = $this->jwtManager->create($user);
        $refreshToken = $this->generateRefreshToken($user);

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phoneNumber' => $user->getPhoneNumber(),
        ];

        $response = new JsonResponse([
            'user' => $userData,
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
                ->withValue($refreshToken)
                ->withHttpOnly(true)
                ->withSecure(true)
                ->withSameSite('strict')
        );

        return $response;
    }

    private function generateRefreshToken(User $user): string
    {
        $payload = [
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'type' => 'refresh',
            'exp' => time() + (30 * 24 * 60 * 60), // 30 days
        ];

        return $this->jwtManager->create($user, $payload);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Authentication failed',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }


    private function getRefreshTokenFromCookie(): ?string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return $currentRequest?->cookies->get(self::REFRESH_COOKIE_NAME);
    }
}