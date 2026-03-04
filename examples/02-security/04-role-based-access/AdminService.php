<?php

require_once '../../../vendor/autoload.php';
require_once 'DemoUser.php';

use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\SecurityContext;
use WebFiori\Http\WebService;

/**
 * Admin panel service - requires ADMIN role
 */
#[RestController('admin', 'Administrative panel service')]
class AdminService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[PreAuthorize("hasRole('ADMIN')")]
    public function getAdminPanel(): array {
        return [
            'message' => 'Welcome to admin panel',
            'user' => SecurityContext::getCurrentUser(),
            'admin_privileges' => [
                'can_delete_users' => true,
                'can_modify_system' => true,
                'can_view_logs' => true
            ],
            'system_stats' => [
                'total_users' => 150,
                'active_sessions' => 23,
                'system_uptime' => '15 days'
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
