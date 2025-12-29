<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\WebService;

#[RestController('param-service')]
class ParameterMappedService extends WebService {
    
    #[GetMapping]
    #[RequestParam('id', 'int', false, null, 'User ID')]
    #[RequestParam('name', 'string', true, 'Anonymous', 'User name')]
    public function getUser() {
        $id = $this->getParamVal('id');
        $name = $this->getParamVal('name');
        $this->sendResponse("User $id: $name");
    }
    
    #[PostMapping]
    #[RequestParam('email', 'email', false)]
    #[RequestParam('age', 'int', true, 18)]
    public function createUser() {
        $email = $this->getParamVal('email');
        $age = $this->getParamVal('age');
        $this->sendResponse("Created user: $email, age: $age");
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === \WebFiori\Http\RequestMethod::GET) {
            $this->getUser();
        } elseif ($method === \WebFiori\Http\RequestMethod::POST) {
            $this->createUser();
        }
    }
}
