<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\SecurityContext;
use WebFiori\Tests\Http\TestServices\SecureService;
use WebFiori\Tests\Http\TestServices\ClassLevelAuthService;

class AuthenticationAnnotationTest extends TestCase {
    
    protected function setUp(): void {
        SecurityContext::clear();
    }
    
    public function testClassLevelAllowAnonymous() {
        $service = new ClassLevelAuthService();
        $this->assertFalse($service->isAuthRequired());
    }
    
    public function testSecurityContextAuthentication() {
        // Test unauthenticated state
        $this->assertFalse(SecurityContext::isAuthenticated());
        
        // Set user and roles
        SecurityContext::setCurrentUser(['id' => 1, 'name' => 'John']);
        SecurityContext::setRoles(['ADMIN', 'USER']);
        SecurityContext::setAuthorities(['USER_CREATE', 'USER_READ']);
        
        $this->assertTrue(SecurityContext::isAuthenticated());
        $this->assertTrue(SecurityContext::hasRole('ADMIN'));
        $this->assertTrue(SecurityContext::hasAuthority('USER_CREATE'));
        $this->assertFalse(SecurityContext::hasRole('GUEST'));
    }
    
    public function testMethodLevelAuthorization() {
        $service = new SecureService();
        
        // Test public method (AllowAnonymous)
        $_GET['action'] = 'public';
        $this->assertTrue($service->checkMethodAuthorization());
        
        // Test private method without auth (RequiresAuth)
        $_GET['action'] = 'private';
        $this->assertFalse($service->checkMethodAuthorization());
        
        // Test private method with auth
        SecurityContext::setCurrentUser(['id' => 1, 'name' => 'John']);
        $this->assertTrue($service->checkMethodAuthorization());
        
        // Test admin method without admin role
        $_GET['action'] = 'admin';
        SecurityContext::setRoles(['USER']);
        $this->assertFalse($service->checkMethodAuthorization());
        
        // Test admin method with admin role
        SecurityContext::setRoles(['ADMIN']);
        $this->assertTrue($service->checkMethodAuthorization());
        
        // Test authority-based method
        $_GET['action'] = 'create';
        SecurityContext::setAuthorities(['USER_READ']);
        $this->assertFalse($service->checkMethodAuthorization());
        
        SecurityContext::setAuthorities(['USER_CREATE']);
        $this->assertTrue($service->checkMethodAuthorization());
    }
    
    public function testSecurityExpressions() {
        $service = new SecureService();
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('evaluateSecurityExpression');
        $method->setAccessible(true);
        
        // Test without authentication
        $this->assertFalse($method->invoke($service, "hasRole('ADMIN')"));
        $this->assertFalse($method->invoke($service, 'isAuthenticated()'));
        $this->assertTrue($method->invoke($service, 'permitAll()'));
        
        // Test with authentication and roles
        SecurityContext::setCurrentUser(['id' => 1]);
        SecurityContext::setRoles(['ADMIN']);
        SecurityContext::setAuthorities(['USER_CREATE']);
        
        $this->assertTrue($method->invoke($service, "hasRole('ADMIN')"));
        $this->assertFalse($method->invoke($service, "hasRole('GUEST')"));
        $this->assertTrue($method->invoke($service, "hasAuthority('USER_CREATE')"));
        $this->assertTrue($method->invoke($service, 'isAuthenticated()'));
    }
    
    protected function tearDown(): void {
        SecurityContext::clear();
        unset($_GET['action']);
    }
}
