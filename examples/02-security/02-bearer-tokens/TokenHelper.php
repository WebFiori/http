<?php

require_once '../../../vendor/autoload.php';

/**
 * Helper class for token generation and validation
 */
class TokenHelper {
    private const SECRET_KEY = 'your-secret-key-here';
    private const TOKEN_EXPIRY = 3600; // 1 hour

    public static function generateToken(array $userData): string {
        $payload = [
            'user' => $userData,
            'iat' => time(),
            'exp' => time() + self::TOKEN_EXPIRY
        ];

        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode($payload));
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", self::SECRET_KEY, true));

        return "$header.$payload.$signature";
    }

    public static function isTokenExpired(string $token): bool {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return true;
        }

        $payloadData = json_decode(base64_decode($parts[1]), true);

        return isset($payloadData['exp']) && $payloadData['exp'] < time();
    }

    public static function validateToken(string $token): ?array {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        // Verify signature
        $expectedSignature = base64_encode(hash_hmac('sha256', "$header.$payload", self::SECRET_KEY, true));

        if ($signature !== $expectedSignature) {
            return null;
        }

        // Decode payload
        $payloadData = json_decode(base64_decode($payload), true);

        if (!$payloadData) {
            return null;
        }

        // Check expiration
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return null; // Token expired
        }

        return $payloadData;
    }
}
