<?php
require_once '../vendor/autoload.php';

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\WebService;

#[RestController('users', 'User management service')]
class UserController extends WebService {
    
    #[GetMapping]
    public function getUsers() {
        $this->sendResponse('Retrieved all users', 'success', 200, ['users' => []]);
    }
    
    #[PostMapping]
    public function createUser() {
        $this->sendResponse('User created', 'success', 201, ['id' => 123]);
    }
    
    #[PutMapping]
    public function updateUser() {
        $this->sendResponse('User updated', 'success', 200);
    }
    
    #[DeleteMapping]
    public function deleteUser() {
        $this->sendResponse('User deleted', 'success', 200);
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        switch ($method) {
            case \WebFiori\Http\RequestMethod::GET:
                $this->getUsers();
                break;
            case \WebFiori\Http\RequestMethod::POST:
                $this->createUser();
                break;
            case \WebFiori\Http\RequestMethod::PUT:
                $this->updateUser();
                break;
            case \WebFiori\Http\RequestMethod::DELETE:
                $this->deleteUser();
                break;
            default:
                $this->sendResponse('Method not allowed', 'error', 405);
        }
    }
}

// Usage example
$service = new UserController();
echo "Service: " . $service->getName() . "\n";
echo "Description: " . $service->getDescription() . "\n";
echo "Supported methods: " . implode(', ', $service->getRequestMethods()) . "\n";
