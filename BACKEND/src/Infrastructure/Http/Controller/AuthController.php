<?php

declare(strict_types=1);

namespace School\Infrastructure\Http\Controller;

use School\Auth\Application\Login\LoginCommand;
use School\Auth\Application\Login\LoginHandler;
use School\Infrastructure\Auth\OAuth\GoogleOAuthClient;
use School\Infrastructure\Http\ApiRequest;
use School\Infrastructure\Http\ApiResponse;

final class AuthController
{
    private GoogleOAuthClient $googleClient;

    public function __construct(
        private readonly LoginHandler $loginHandler,
        string $clientId,
        string $clientSecret,
        string $redirectUri
    ) {
        if ($clientId === 'XXX' || empty($clientId) || $clientSecret === 'XXX' || empty($clientSecret)) {
            throw new \RuntimeException('configura google oauth en .env');
        }
        $this->googleClient = new GoogleOAuthClient($clientId, $clientSecret, $redirectUri);
    }

    public function login(): void
    {
        $authUrl = $this->googleClient->getAuthUrl();
        ApiResponse::json(200, ['auth_url' => $authUrl]);
    }

    public function callback(ApiRequest $request): void
    {
        $query = $request->getQuery();
        $code = $query['code'] ?? null;

        if (empty($code)) {
            $this->redirectToFrontendWithError('falta codigo de autorizacion');
            return;
        }

        try {
            $tokenData = $this->googleClient->exchangeCode($code);
            $accessToken = $tokenData['access_token'];
            $googleUser = $this->googleClient->getUserInfo($accessToken);

            $command = new LoginCommand(
                $googleUser->id,
                $googleUser->email,
                $googleUser->name
            );

            $jwt = $this->loginHandler->handle($command);
            $this->redirectToFrontendWithToken($jwt, $googleUser);
        } catch (\Throwable $e) {
            $this->redirectToFrontendWithError('error de autenticacion');
        }
    }

    private function redirectToFrontendWithToken(string $token, object $user): void
    {
        $frontendUrl = getenv('SCHOOL_FRONTEND_ORIGIN') ?: 'http://localhost:8001';
        $params = http_build_query([
            'token' => $token,
            'google_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
        header('Location: ' . $frontendUrl . '/?' . $params);
    }

    private function redirectToFrontendWithError(string $error): void
    {
        $frontendUrl = getenv('SCHOOL_FRONTEND_ORIGIN') ?: 'http://localhost:8001';
        header('Location: ' . $frontendUrl . '/?error=' . urlencode($error));
    }
}
