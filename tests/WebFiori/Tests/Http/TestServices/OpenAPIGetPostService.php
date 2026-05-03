<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

/**
 * Test service with both GET and POST methods, each with different parameters.
 * Used to test OpenAPI spec generation for mixed-method services.
 */
#[RestController('get-post-service')]
class OpenAPIGetPostService extends WebService {
    public function isAuthorized(): bool {
        return true;
    }

    #[GetMapping]
    #[RequestParam(name: 'id', type: ParamType::INT)]
    public function getItem() {
        // stub
    }

    #[PostMapping]
    #[RequestParam(name: 'name', type: ParamType::STRING)]
    #[RequestParam(name: 'email', type: ParamType::EMAIL)]
    #[RequestParam(name: 'nickname', type: ParamType::STRING, optional: true)]
    public function createItem() {
        // stub
    }

    public function processRequest() {
    }
}
