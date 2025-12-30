<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\AllowAnonymous;

/**
 * A service that accepts a name parameter and returns a personalized greeting
 */
#[RestController('greeting', 'A greeting service with name parameter')]
class GreetingService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', 'string', true, null, 'Name to include in greeting (1-50 chars)')]
    public function greet(?string $name = null): string {
        if ($name !== null) {
            return "Hello, $name!";
        } else {
            return 'Hello, Guest!';
        }
    }
}
