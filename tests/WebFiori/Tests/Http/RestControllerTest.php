<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AnnotatedService;
use WebFiori\Tests\Http\TestServices\NonAnnotatedService;

class RestControllerTest extends TestCase {
    
    public function testAnnotatedServiceName() {
        $service = new AnnotatedService();
        $this->assertEquals('annotated-service', $service->getName());
    }
    
    public function testAnnotatedServiceDescription() {
        $service = new AnnotatedService();
        $this->assertEquals('A service configured via annotations', $service->getDescription());
    }
    
    public function testNonAnnotatedService() {
        $service = new NonAnnotatedService();
        $this->assertEquals('non-annotated', $service->getName());
        $this->assertEquals('A traditional service', $service->getDescription());
    }
    
    public function testAnnotationWithEmptyName() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('fallback-name');
            }
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $this->assertEquals('fallback-name', $service->getName());
    }
    
    public function testAnnotationWithoutFallback() {
        $service = new class extends \WebFiori\Http\WebService {
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $this->assertEquals('new-service', $service->getName());
    }
    
    public function testAnnotatedServiceWithManager() {
        $manager = new WebServicesManager();
        $service = new AnnotatedService();
        $manager->addService($service);
        
        $retrievedService = $manager->getServiceByName('annotated-service');
        $this->assertNotNull($retrievedService);
        $this->assertEquals('A service configured via annotations', $retrievedService->getDescription());
    }
}
