<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\WebService;

#[RestController('mixed-service')]
class MixedResponseService extends WebService {
    
    // ResponseBody method with authentication
    #[GetMapping]
    #[ResponseBody]
    #[PreAuthorize("hasRole('USER')")]
    public function getSecureData(): array {
        return ['secure' => 'data', 'user' => 'authenticated'];
    }
    
    // ResponseBody method without authentication
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    public function getPublicData(): array {
        return ['public' => 'data', 'access' => 'open'];
    }
    
    // Traditional method (no ResponseBody)
    #[PostMapping]
    #[AllowAnonymous]
    public function traditionalMethod(): void {
        $this->sendResponse('Traditional method response', 200, 'success', ['method' => 'traditional']);
    }
    
    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
        $action = $_GET['action'] ?? 'traditional';
        if ($action === 'traditional') {
            $this->traditionalMethod();
        } else {
            $this->sendResponse('Unknown action', 400, 'error');
        }
    }
    
    protected function getCurrentProcessingMethod(): ?string {
        $action = $_GET['action'] ?? 'traditional';
        return match($action) {
            'secure' => 'getSecureData',
            'public' => 'getPublicData',
            'traditional' => 'traditionalMethod',
            default => null
        };
    }
}
