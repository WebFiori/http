<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\OpenAPI\OpenAPIGenerator;
use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\ParamType;

/**
 * Example service for OpenAPI generation.
 */
#[RestController('users', 'User management')]
class UserService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('id', ParamType::INT, true)]
    public function getUser(?int $id): array {
        return ['id' => $id ?? 1, 'name' => 'John'];
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', ParamType::STRING)]
    #[RequestParam('email', ParamType::EMAIL)]
    public function createUser(string $name, string $email): array {
        return ['id' => 2, 'name' => $name, 'email' => $email];
    }

    public function isAuthorized(): bool { return true; }
    public function processRequest() {}
}

// Generate OpenAPI spec using the standalone generator
$generator = new OpenAPIGenerator();
$spec = $generator->generate(
    [new UserService()],
    'User Management API',
    '1.0.0',
    '/api/v1'
);

// Output as JSON
header('Content-Type: application/json');
echo $spec->toJSON();
