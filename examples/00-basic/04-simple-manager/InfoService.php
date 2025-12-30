<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\WebService;

/**
 * API information service that provides metadata about the API
 */
#[RestController('info', 'API information and metadata')]
class InfoService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getApiInfo(): array {
        return [
            'name' => 'Multi-Service API',
            'version' => '1.0.0',
            'description' => 'Demonstration of multiple services in one manager',
            'services' => ['info', 'users', 'products'],
            'endpoints' => [
                'GET /info' => 'Get API information',
                'GET /users' => 'Get all users',
                'GET /products' => 'Get all products'
            ]
        ];
    }
}
