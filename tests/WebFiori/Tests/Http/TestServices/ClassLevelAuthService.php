<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\WebService;

#[RestController('public-service')]
#[AllowAnonymous]
class ClassLevelAuthService extends WebService {
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $this->sendResponse('Public service - no auth required');
    }
}
