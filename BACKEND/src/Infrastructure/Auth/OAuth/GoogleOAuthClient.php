<?php

declare(strict_types=1);

namespace School\Infrastructure\Auth\OAuth;

use RuntimeException;

final class GoogleOAuthClient
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $redirectUri
    ) {
    }

    public function getAuthUrl(): string
    {
        return self::AUTH_URL . '?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
        ]);
    }

    /**
     * Intercanvia el codi d'autorització per un access token de Google.
     *
     * @return array{access_token: string, token_type: string, expires_in: int}
     */
    public function exchangeCode(string $code): array
    {
        $body = http_build_query([
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body,
        ]]);

        $response = @file_get_contents(self::TOKEN_URL, false, $context);

        if ($response === false) {
            throw new RuntimeException('Failed to exchange OAuth code for token');
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            throw new RuntimeException('OAuth error: ' . ($data['error_description'] ?? $data['error']));
        }

        if (!isset($data['access_token'])) {
            throw new RuntimeException('OAuth token response missing access_token');
        }

        return $data;
    }

    public function getUserInfo(string $accessToken): GoogleUser
    {
        $context = stream_context_create(['http' => [
            'header' => "Authorization: Bearer $accessToken\r\n",
        ]]);

        $response = @file_get_contents(self::USERINFO_URL, false, $context);

        if ($response === false) {
            throw new RuntimeException('Failed to fetch Google user info');
        }

        $data = json_decode($response, true);

        return new GoogleUser(
            id: $data['sub'],
            email: $data['email'],
            name: $data['name']
        );
    }
}
