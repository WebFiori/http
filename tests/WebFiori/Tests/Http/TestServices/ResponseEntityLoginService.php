<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

#[RestController('response-entity-login')]
class ResponseEntityLoginService extends WebService {

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('username', ParamType::STRING)]
    #[RequestParam('password', ParamType::STRING)]
    public function login(string $username, string $password): ResponseEntity {
        if ($username === 'admin' && $password === 'secret') {
            return ResponseEntity::ok(new Json(['token' => 'abc123']));
        }
        if ($username === 'banned') {
            return ResponseEntity::forbidden(new Json(['message' => 'Account suspended']));
        }
        return ResponseEntity::unauthorized(new Json(['message' => 'Invalid credentials']));
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}
