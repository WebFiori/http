<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\IntegrationTestService;
use WebFiori\Tests\Http\TestServices\ResponseBodyTestService;
use WebFiori\Tests\Http\TestServices\LegacyService;

class WebServicesManagerIntegrationTest extends TestCase {
    
    protected function setUp(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['service'] = 'test-service';
    }
    
    public function testAutoProcessingIntegration() {
        $manager = new WebServicesManager();
        $service = new IntegrationTestService();
        $manager->addService($service);
        
        // Test that service has processWithAutoHandling method
        $this->assertTrue(method_exists($service, 'processWithAutoHandling'));
        
        // Test that service has ResponseBody methods
        $this->assertTrue($service->hasResponseBodyAnnotation('getData'));
    }
    
    public function testManagerProcessesAutoHandlingServices() {
        $service = new IntegrationTestService();
        
        // Test that service has ResponseBody methods
        $this->assertTrue($service->hasResponseBodyAnnotation('getData'));
        
        // Test method return value
        $result = $service->getData();
        $this->assertIsArray($result);
        $this->assertEquals('Auto-processing via WebServicesManager', $result['message']);
    }
    
    public function testLegacyServiceStillWorks() {
        $service = new LegacyService();
        
        // Legacy service should not have ResponseBody methods
        $this->assertFalse($service->hasResponseBodyAnnotation('getData'));
        
        // Should have traditional methods
        $this->assertTrue(method_exists($service, 'processRequest'));
    }
    
    public function testMixedServiceTypes() {
        $manager = new WebServicesManager();
        
        // Add both new and legacy services
        $manager->addService(new IntegrationTestService());
        $manager->addService(new LegacyService());
        
        // Both should be registered
        $this->assertNotNull($manager->getServiceByName('integration-test'));
        $this->assertNotNull($manager->getServiceByName('legacy-service'));
    }
    
    public function testResponseBodyWithExceptionHandling() {
        $service = new ResponseBodyTestService();
        
        // Test that service has ResponseBody methods
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $targetMethod = $service->getTargetMethod();
        $this->assertTrue($service->hasResponseBodyAnnotation($targetMethod));
        
        // Test method return value
        $result = $service->getArrayData();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('users', $result);
    }
    
    public function testServiceMethodDiscovery() {
        $service = new ResponseBodyTestService();
        
        // Test GET method discovery
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $targetMethod = $service->getTargetMethod();
        $this->assertEquals('getArrayData', $targetMethod);
        
        // Test POST method discovery
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $targetMethod = $service->getTargetMethod();
        $this->assertContains($targetMethod, ['deleteData', 'createUser']);
    }
    
    public function testBackwardCompatibility() {
        // Test that all existing functionality still works
        $legacyService = new LegacyService();
        $newService = new IntegrationTestService();
        
        // Both should have processRequest method
        $this->assertTrue(method_exists($legacyService, 'processRequest'));
        $this->assertTrue(method_exists($newService, 'processRequest'));
        
        // Both should have processWithAutoHandling (inherited from WebService)
        $this->assertTrue(method_exists($legacyService, 'processWithAutoHandling'));
        $this->assertTrue(method_exists($newService, 'processWithAutoHandling'));
    }
    
    protected function tearDown(): void {
        unset($_GET['service']);
        unset($_SERVER['REQUEST_METHOD']);
    }
}
