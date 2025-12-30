<?php

require_once '../../../vendor/autoload.php';
require_once 'DemoUser.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\SecurityContext;

/**
 * User profile service - requires authentication
 */
#[RestController('user', 'User profile and information service')]
class UserService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[RequiresAuth]
    public function getUserInfo(): array {
        return [
            'user' => SecurityContext::getCurrentUser(),
            'roles' => SecurityContext::getRoles(),
            'authorities' => SecurityContext::getAuthorities(),
            'is_authenticated' => SecurityContext::isAuthenticated(),
            'access_time' => date('Y-m-d H:i:s')
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
