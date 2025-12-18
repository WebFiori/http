<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

#[RestController('annotated-service', 'A service configured via annotations')]
class AnnotatedService extends WebService {
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $this->sendResponse('Annotated service response');
    }
}
