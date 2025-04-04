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

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
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

class CustomJWTAuthenticator extends AbstractAuthenticator
{
    private JWTTokenManagerInterface $jwtManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
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
        $refreshToken = $credentials['refresh_token'] ?? null;

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
        $refreshToken = $this->jwtManager->create($user);
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phoneNumber' => $user->getPhoneNumber(),
        ];

        // Return response
        return new JsonResponse([
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $userData,
        ]);
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

    private function isMobileClient(Request $request): bool
    {
        $userAgent = $request->headers->get('User-Agent', '');

        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Authentication failed',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
