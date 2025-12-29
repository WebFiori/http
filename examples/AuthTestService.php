<?php
require_once '../vendor/autoload.php';

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\WebService;

#[RestController('auth-test', 'Authentication test service')]
class AuthTestService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[PreAuthorize("hasAuthority('SUPER_ADMIN')")]
    public function restrictedEndpoint(): array {
        return ['message' => 'You have super admin access!'];
    }
}
