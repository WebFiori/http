<?php

require_once '../../../vendor/autoload.php';
require_once 'TokenHelper.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Json\Json;

#[RestController('login', 'Login service to get Bearer token')]
class LoginService extends WebService {
    
    private const USERS = [
        'admin' => ['password' => 'password123', 'role' => 'admin'],
        'user' => ['password' => 'userpass', 'role' => 'user'],
        'guest' => ['password' => 'guestpass', 'role' => 'guest']
    ];
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('username', 'string', false, null, 'Username')]
    #[RequestParam('password', 'string', false, null, 'Password')]
    public function login(string $username, string $password): array {
        // Validate credentials
        if (!isset(self::USERS[$username]) || self::USERS[$username]['password'] !== $password) {
            throw new \InvalidArgumentException('Invalid credentials');
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
    
}
