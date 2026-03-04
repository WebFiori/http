<?php

require_once '../../../vendor/autoload.php';
require_once 'TokenHelper.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

/**
 * Service demonstrating Bearer token authentication
 */
#[RestController('auth', 'Bearer token authentication service')]
class TokenAuthService extends WebService {
    private const USERS = [
        'admin' => ['password' => 'password123', 'role' => 'admin'],
        'user' => ['password' => 'userpass', 'role' => 'user'],
        'guest' => ['password' => 'guestpass', 'role' => 'guest']
    ];

    private ?array $currentUser = null;

    #[GetMapping]
    #[ResponseBody]
    #[RequiresAuth]
    #[RequestParam('operation', 'string', false, null, 'Operation: profile, refresh')]
    public function handleAuthenticatedAction(?string $operation = 'profile'): array {
        // Extract user from token
        $authHeader = $this->getAuthHeader();
        $token = $authHeader->getCredentials();
        $tokenData = TokenHelper::validateToken($token);
        $this->currentUser = $tokenData['user'];

        switch ($operation) {
            case 'profile':
                return $this->handleProfile();
            case 'refresh':
                return $this->handleRefresh();
            default:
                throw new InvalidArgumentException('Unknown operation');
        }
    }

    public function isAuthorized(): bool {
        // All actions require Bearer token (login uses AllowAnonymous)
        $authHeader = $this->getAuthHeader();

        if ($authHeader === null) {
            return false;
        }

        $scheme = $authHeader->getScheme();
        $token = $authHeader->getCredentials();

        if ($scheme !== 'bearer') {
            return false;
        }

        // Validate token
        $tokenData = TokenHelper::validateToken($token);

        return $tokenData !== null;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('operation', 'string', false, null, 'Operation: login')]
    public function login(?string $operation = 'login'): array {
        $inputs = $this->getInputs();

        if (!($inputs instanceof Json)) {
            throw new InvalidArgumentException('JSON body required for login');
        }

        $username = $inputs->get('username');
        $password = $inputs->get('password');

        if (!$username || !$password) {
            throw new InvalidArgumentException('Username and password required');
        }

        // Validate credentials
        if (!isset(self::USERS[$username]) || self::USERS[$username]['password'] !== $password) {
            throw new InvalidArgumentException('Invalid credentials');
        }

        // Generate token
        $userData = [
            'username' => $username,
            'role' => self::USERS[$username]['role'],
            'login_time' => date('Y-m-d H:i:s')
        ];

        $token = TokenHelper::generateToken($userData);

        return [
            'token' => $token,
            'user' => $userData,
            'expires_in' => 3600
        ];
    }

    private function getUserPermissions(string $role): array {
        switch ($role) {
            case 'admin':
                return ['read', 'write', 'delete', 'admin'];
            case 'user':
                return ['read', 'write'];
            case 'guest':
                return ['read'];
            default:
                return [];
        }
    }

    private function handleProfile(): array {
        if (!$this->currentUser) {
            throw new RuntimeException('User context not available');
        }

        return [
            'user' => $this->currentUser,
            'permissions' => $this->getUserPermissions($this->currentUser['role']),
            'last_access' => date('Y-m-d H:i:s')
        ];
    }

    private function handleRefresh(): array {
        if (!$this->currentUser) {
            throw new RuntimeException('User context not available');
        }

        // Generate new token with updated timestamp
        $userData = $this->currentUser;
        $userData['refresh_time'] = date('Y-m-d H:i:s');

        $newToken = TokenHelper::generateToken($userData);

        return [
            'token' => $newToken,
            'user' => $userData,
            'expires_in' => 3600
        ];
    }
}
