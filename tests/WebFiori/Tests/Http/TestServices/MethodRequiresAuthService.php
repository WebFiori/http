<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

/**
 * Service with method-level #[RequiresAuth] only.
 * isAuthorized() returns false to prove it's not called.
 */
#[RestController('method-requires-auth')]
class MethodRequiresAuthService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[RequiresAuth]
    public function getSecretData(): Json {
        $json = new Json();
        $json->add('secret', 'method-level-protected');
        return $json;
    }

    public function isAuthorized(): bool {
        return false; // Should NOT matter when #[RequiresAuth] is on method
    }

    public function processRequest() {
    }
}
