<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\AllowAnonymous;

/**
 * A minimal web service that responds with "Hello World!"
 */
#[RestController('hello', 'A simple hello world service')]
class HelloService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function sayHello(): string {
        return 'Hello World!';
    }
}
