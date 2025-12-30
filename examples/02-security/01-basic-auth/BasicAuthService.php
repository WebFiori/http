<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * Service demonstrating HTTP Basic authentication
 */
#[RestController('secure', 'Secure service with Basic authentication')]
class BasicAuthService extends WebService {
    // Predefined users (in real app, use database)
    private const USERS = [
        'admin' => 'password123',
        'user' => 'userpass',
        'guest' => 'guestpass'
    ];

    #[GetMapping]
    #[ResponseBody]
    #[RequiresAuth]
    public function getSecureData(): array {
        // Get authenticated user info
        $authHeader = $this->getAuthHeader();
        $credentials = base64_decode($authHeader->getCredentials());
        [$username] = explode(':', $credentials, 2);

        return [
            'user' => $username,
            'authenticated_at' => date('Y-m-d H:i:s'),
            'auth_method' => 'basic',
            'secure_data' => [
                'secret_key' => 'abc123xyz',
                'access_level' => $this->getUserAccessLevel($username),
                'session_id' => uniqid('sess_')
            ]
        ];
    }

    public function isAuthorized(): bool {
        $authHeader = $this->getAuthHeader();

        if ($authHeader === null) {
            return false;
        }

        $scheme = $authHeader->getScheme();
        $credentials = $authHeader->getCredentials();

        // Check if it's Basic authentication
        if ($scheme !== 'basic') {
            return false;
        }

        // Decode base64 credentials
        $decoded = base64_decode($credentials);

        if ($decoded === false) {
            return false;
        }

        // Split username and password
        $parts = explode(':', $decoded, 2);

        if (count($parts) !== 2) {
            return false;
        }

        [$username, $password] = $parts;

        // Validate credentials
        return $this->validateUser($username, $password);
    }

    private function getUserAccessLevel(string $username): string {
        switch ($username) {
            case 'admin':
                return 'administrator';
            case 'user':
                return 'standard_user';
            case 'guest':
                return 'read_only';
            default:
                return 'unknown';
        }
    }

    private function validateUser(string $username, string $password): bool {
        if (!isset(self::USERS[$username])) {
            return false;
        }

        if (self::USERS[$username] !== $password) {
            return false;
        }

        return true;
    }
}
