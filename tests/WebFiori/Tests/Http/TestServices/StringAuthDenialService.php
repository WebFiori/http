<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;

#[RestController('string-auth-denial')]
class StringAuthDenialService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    public function getData(): array {
        return ['data' => 'secret'];
    }

    public function isAuthorized(): string|bool {
        return 'You must be a premium member to access this resource.';
    }

    public function processRequest() {
    }
}
