<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\WebService;

#[RestController('mapped-methods')]
class MappedMethodsService extends WebService {
    
    #[GetMapping]
    public function getUsers() {
        $this->sendResponse('GET users');
    }
    
    #[PostMapping]
    public function createUser() {
        $this->sendResponse('POST user');
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $method = $this->getManager()->getRequestMethod();
        if ($method === \WebFiori\Http\RequestMethod::GET) {
            $this->getUsers();
        } elseif ($method === \WebFiori\Http\RequestMethod::POST) {
            $this->createUser();
        }
    }
}
