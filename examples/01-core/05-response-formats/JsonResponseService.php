<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;

#[RestController('json-response', 'JSON response service')]
class JsonResponseService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getJsonData(): array {
        return [
            'message' => 'Hello from WebFiori HTTP',
            'timestamp' => date('Y-m-d H:i:s'),
            'format' => 'json',
            'server_info' => [
                'php_version' => PHP_VERSION,
                'server_time' => time()
            ]
        ];
    }
}
