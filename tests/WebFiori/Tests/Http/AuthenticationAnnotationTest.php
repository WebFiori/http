<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\SecurityContext;
use WebFiori\Tests\Http\TestUser;
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
        SecurityContext::setCurrentUser(new TestUser(1, ['ADMIN', 'USER'], ['USER_CREATE', 'USER_READ'], true));
        
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
        SecurityContext::setCurrentUser(new TestUser(1, [], [], true));
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
        SecurityContext::clear();
        
        // Test without authentication
        $this->assertFalse(SecurityContext::evaluateExpression("hasRole('ADMIN')"));
        $this->assertFalse(SecurityContext::evaluateExpression('isAuthenticated()'));
        $this->assertTrue(SecurityContext::evaluateExpression('permitAll()'));
        
        // Test with authentication and roles
        SecurityContext::setCurrentUser(new TestUser(1, ['ADMIN'], ['USER_CREATE'], true));
        
        $this->assertTrue(SecurityContext::evaluateExpression("hasRole('ADMIN')"));
        $this->assertFalse(SecurityContext::evaluateExpression("hasRole('GUEST')"));
        $this->assertTrue(SecurityContext::evaluateExpression("hasAuthority('USER_CREATE')"));
        $this->assertTrue(SecurityContext::evaluateExpression('isAuthenticated()'));
    }
    
    protected function tearDown(): void {
        SecurityContext::clear();
        unset($_GET['action']);
    }
}
