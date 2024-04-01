<?php

namespace App\Service;

class KeyManagementService
{
    public function generatePublicKeyResponseBody(string $key): array
    {
        $publicKey = str_replace('/r/n', PHP_EOL, $key);

        $data = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));

        $modulus = base64_encode($data['rsa']['n']);
        $exponent = base64_encode($data['rsa']['e']);

        return [
            'keys' => [
                'kty' => 'RSA',
                'use' => 'mul',
                'n' => $modulus,
                'e' => $exponent
            ]
        ];
    }

}