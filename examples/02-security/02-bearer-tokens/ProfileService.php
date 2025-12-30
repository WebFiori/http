<?php

require_once '../../../vendor/autoload.php';
require_once 'TokenHelper.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequiresAuth;

#[RestController('profile', 'Get user profile with Bearer token')]
class ProfileService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[RequiresAuth]
    public function getProfile(): array {
        $authHeader = $this->getAuthHeader();
        $token = $authHeader->getCredentials();
        $tokenData = TokenHelper::validateToken($token);
        $user = $tokenData['user'];
        
        return [
            'user' => $user,
            'permissions' => $this->getUserPermissions($user['role']),
            'last_access' => date('Y-m-d H:i:s')
        ];
    }
    
    public function isAuthorized(): bool {
        $authHeader = $this->getAuthHeader();
        
        if ($authHeader === null || $authHeader->getScheme() !== 'bearer') {
            return false;
        }
        
        $token = $authHeader->getCredentials();
        $tokenData = TokenHelper::validateToken($token);
        
        return $tokenData !== null;
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
}
