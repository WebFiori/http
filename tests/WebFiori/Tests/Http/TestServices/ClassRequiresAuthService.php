<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

/**
 * Service with class-level #[RequiresAuth].
 * isAuthorized() intentionally returns false to prove SecurityContext is used instead.
 */
#[RestController('class-requires-auth')]
#[RequiresAuth]
class ClassRequiresAuthService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    public function getProtectedData(): Json {
        $json = new Json();
        $json->add('secret', 'class-level-protected');
        return $json;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function publicEndpoint(): Json {
        $json = new Json();
        $json->add('public', true);
        return $json;
    }

    public function isAuthorized(): bool {
        return false; // Should NOT matter when #[RequiresAuth] is on class
    }

    public function processRequest() {
    }
}
