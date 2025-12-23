<?php

use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PreAuthorize;
use WebFiori\Http\Annotations\RequiresAuth;
use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\WebService;
use WebFiori\Http\SecurityContext;
use WebFiori\Http\WebServicesManager;

#[RestController('auth-demo', 'Authentication demonstration service')]
class AuthenticatedController extends WebService {
    
    #[GetMapping]
    #[AllowAnonymous]
    public function getPublicInfo() {
        $this->sendResponse('This is public information - no authentication required');
    }
    
    #[GetMapping]
    #[RequiresAuth]
    public function getProfile() {
        $user = SecurityContext::getCurrentUser();
        $this->sendResponse('User profile', 200, 'success', [
            'user' => $user,
            'roles' => SecurityContext::getRoles(),
            'authorities' => SecurityContext::getAuthorities()
        ]);
    }
    
    #[PostMapping]
    #[PreAuthorize("hasRole('ADMIN')")]
    public function adminOperation() {
        $this->sendResponse('Admin operation completed successfully');
    }
    
    #[PostMapping]
    #[PreAuthorize("hasAuthority('USER_MANAGE')")]
    public function manageUsers() {
        $this->sendResponse('User management operation completed');
    }
    
    public function isAuthorized(): bool {
        // This is the fallback authorization check
        // In a real application, you might check JWT tokens, session, etc.
        return true;
    }

    public function processRequest() {
        // Check method-level authorization first
        if (!$this->checkMethodAuthorization()) {
            $this->sendResponse('Access denied', 403, 'error');
            return;
        }
        
        $action = $_GET['action'] ?? 'public';
        
        switch ($action) {
            case 'public':
                $this->getPublicInfo();
                break;
            case 'profile':
                $this->getProfile();
                break;
            case 'admin':
                $this->adminOperation();
                break;
            case 'manage':
                $this->manageUsers();
                break;
            default:
                $this->sendResponse('Unknown action', 400, 'error');
        }
    }
    
    protected function getCurrentProcessingMethod(): ?string {
        $action = $_GET['action'] ?? 'public';
        return match($action) {
            'public' => 'getPublicInfo',
            'profile' => 'getProfile',
            'admin' => 'adminOperation',
            'manage' => 'manageUsers',
            default => null
        };
    }
}

// Demo usage
// echo "=== Authentication Demo ===\n";

// $controller = new AuthenticatedController();

// // Test 1: Public access (no auth required)
// echo "\n1. Testing public access:\n";
// $_GET['action'] = 'public';
// $controller->processRequest();

// // Test 2: Private access without authentication
// echo "\n2. Testing private access without auth:\n";
// $_GET['action'] = 'profile';
// $controller->processRequest();

// Test 3: Set up authentication
// echo "\n3. Setting up authentication:\n";
// SecurityContext::setCurrentUser(['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com']);
// SecurityContext::setRoles(['USER']);
// SecurityContext::setAuthorities(['USER_READ']);

// echo "User authenticated: " . (SecurityContext::isAuthenticated() ? 'Yes' : 'No') . "\n";
// echo "Roles: " . implode(', ', SecurityContext::getRoles()) . "\n";
// echo "Authorities: " . implode(', ', SecurityContext::getAuthorities()) . "\n";

// // Test 4: Private access with authentication
// echo "\n4. Testing private access with auth:\n";
// $_GET['action'] = 'profile';
// $controller->processRequest();

// // Test 5: Admin access without admin role
// echo "\n5. Testing admin access without admin role:\n";
// $_GET['action'] = 'admin';
// $controller->processRequest();

// // Test 6: Grant admin role and try again
// echo "\n6. Granting admin role and testing admin access:\n";
// SecurityContext::setRoles(['USER', 'ADMIN']);
// $controller->processRequest();

// // Test 7: Authority-based access
// echo "\n7. Testing authority-based access:\n";
// $_GET['action'] = 'manage';
// $controller->processRequest();

// // Test 8: Grant required authority
// echo "\n8. Granting USER_MANAGE authority:\n";
// SecurityContext::setAuthorities(['USER_READ', 'USER_MANAGE']);
// $controller->processRequest();

// // Cleanup
// SecurityContext::clear();
// unset($_GET['action']);
