<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

readonly class JwtService
{

    public function __construct(private string $privateKey, private string $publicKey)
    {
    }

    public function generateJwtForUser(User $user): string
    {

        $privateKey = str_replace('/r/n', PHP_EOL, $this->privateKey);


        $payload = [
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'funds' => $user->getFunds(),
            'created_at' => time(),
            'expires_in' => 300
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public function isExpired(string $jwt): bool
    {
        $decoded = (array) JWT::decode($jwt, new Key($this->publicKey, 'RS256'));

        return time() > $decoded['created_at'] + $decoded['expires_in'] ;
    }
}