<?php

require_once '../../../vendor/autoload.php';
require_once 'DemoUser.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\SecurityContext;
use WebFiori\Http\WebService;

/**
 * Public information service - no authentication required
 */
#[RestController('public', 'Public information service')]
class PublicService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getPublicInfo(): array {
        return [
            'message' => 'This is public information - no authentication required',
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => [
                'php_version' => PHP_VERSION,
                'authenticated' => SecurityContext::isAuthenticated()
            ]
        ];
    }

    public function isAuthorized(): bool {
        return true; // Public service, no auth needed
    }
}
