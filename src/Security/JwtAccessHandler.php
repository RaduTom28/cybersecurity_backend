<?php

namespace App\Security;

use App\Service\JwtService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use MongoDB\Driver\Exception\AuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use function PHPUnit\Framework\throwException;

readonly class JwtAccessHandler implements AccessTokenHandlerInterface
{
    public function __construct(private string $publicKey, private JwtService $jwtService)
    {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {

        try {
            $decoded = (array) JWT::decode($accessToken, new Key($this->publicKey, 'RS256'));
            if ($this->jwtService->isExpired($accessToken)) {
                throw new Exception('expired token');
            }
        }catch (Exception $exception) {
            throw new AuthenticationException('Token Decoding Failed: '. $exception->getMessage());
        }

        return new UserBadge($decoded['email']);
    }
}