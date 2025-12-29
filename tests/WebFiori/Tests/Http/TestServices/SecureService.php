<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\WebService;
use WebFiori\Http\SecurityContext;

#[RestController('secure-service')]
class SecureService extends WebService {
    
    #[GetMapping]
    #[AllowAnonymous]
    public function getPublicData() {
        $this->sendResponse('Public data - no auth required');
    }
    
    #[GetMapping]
    #[RequiresAuth]
    public function getPrivateData() {
        $user = SecurityContext::getCurrentUser();
        $this->sendResponse('Private data for: ' . ($user['name'] ?? 'unknown'));
    }
    
    #[PostMapping]
    #[PreAuthorize("hasRole('ADMIN')")]
    public function adminOnly() {
        $this->sendResponse('Admin-only operation');
    }
    
    #[PostMapping]
    #[PreAuthorize("hasAuthority('USER_CREATE')")]
    public function createUser() {
        $this->sendResponse('User created');
    }
    
    public function isAuthorized(): bool {
        return true; // Default fallback
    }

    public function processRequest() {
        if (!$this->checkMethodAuthorization()) {
            $this->sendResponse('Unauthorized', 'error', 401);
            return;
        }
        
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $action = $_GET['action'] ?? 'public';
        
        switch ($action) {
            case 'public':
                $this->getPublicData();
                break;
            case 'private':
                $this->getPrivateData();
                break;
            case 'admin':
                $this->adminOnly();
                break;
            case 'create':
                $this->createUser();
                break;
        }
    }
    
    protected function getCurrentProcessingMethod(): ?string {
        $action = $_GET['action'] ?? 'public';
        return match($action) {
            'public' => 'getPublicData',
            'private' => 'getPrivateData', 
            'admin' => 'adminOnly',
            'create' => 'createUser',
            default => null
        };
    }
}
