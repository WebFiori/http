<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ResponseEntity;
use WebFiori\Http\WebService;
use WebFiori\Json\Json;

#[RestController('response-entity-misc')]
class ResponseEntityMiscService extends WebService {

    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function serverError(): ResponseEntity {
        return ResponseEntity::error(new Json(['message' => 'Something went wrong']));
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}
