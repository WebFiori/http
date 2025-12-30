<?php

require_once '../../../vendor/autoload.php';
require_once 'DemoUser.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\SecurityContext;

/**
 * User management service - requires USER_MANAGE authority
 */
#[RestController('user-manager', 'User management operations service')]
class UserManagerService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[PreAuthorize("hasAuthority('USER_MANAGE')")]
    public function manageUsers(): array {
        return [
            'message' => 'User management operation completed',
            'user' => SecurityContext::getCurrentUser(),
            'available_operations' => [
                'create_user' => SecurityContext::hasAuthority('USER_CREATE'),
                'update_user' => SecurityContext::hasAuthority('USER_UPDATE'),
                'delete_user' => SecurityContext::hasAuthority('USER_DELETE'),
                'view_users' => SecurityContext::hasAuthority('USER_READ')
            ],
            'managed_users' => [
                ['id' => 1, 'name' => 'John Doe', 'status' => 'active'],
                ['id' => 2, 'name' => 'Jane Smith', 'status' => 'inactive']
            ]
        ];
    }
    
    public function isAuthorized(): bool {
        $demoUser = new DemoUser(
            id: 1,
            name: 'Demo User',
            roles: ['USER', 'ADMIN'],
            authorities: ['USER_CREATE', 'USER_UPDATE', 'USER_DELETE', 'USER_READ', 'USER_MANAGE']
        );
        
        SecurityContext::setCurrentUser($demoUser);
        return true;
    }
}
