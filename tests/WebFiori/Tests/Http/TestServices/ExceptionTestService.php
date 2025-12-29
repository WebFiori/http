<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Exceptions\NotFoundException;
use WebFiori\Http\Exceptions\BadRequestException;
use WebFiori\Http\Exceptions\UnauthorizedException;
use WebFiori\Http\WebService;

#[RestController('exception-test')]
class ExceptionTestService extends WebService {
    
    #[GetMapping]
    #[ResponseBody]
    #[RequestParam('id', 'int')]
    public function getUser(): array {
        $id = $this->getParamVal('id');
        
        // For testing, check if id is set via test
        if (!$id && isset($_GET['test_id'])) {
            $id = (int)$_GET['test_id'];
        }
        
        if ($id === 404) {
            throw new NotFoundException('User not found');
        }
        
        if ($id === 400) {
            throw new BadRequestException('Invalid user ID');
        }
        
        return ['user' => ['id' => $id, 'name' => 'Test User']];
    }
    
    #[PostMapping]
    #[ResponseBody]
    public function createUser(): array {
        throw new UnauthorizedException('Authentication required');
    }
    
    #[GetMapping]
    #[ResponseBody]
    public function getError(): array {
        throw new \Exception('Generic error');
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $action = $_GET['action'] ?? 'get';
        switch ($action) {
            case 'get':
                $this->getUser();
                break;
            case 'create':
                $this->createUser();
                break;
            case 'error':
                $this->getError();
                break;
        }
    }
    
    protected function getCurrentProcessingMethod(): ?string {
        $action = $_GET['action'] ?? 'get';
        return match($action) {
            'get' => 'getUser',
            'create' => 'createUser',
            'error' => 'getError',
            default => null
        };
    }
}
