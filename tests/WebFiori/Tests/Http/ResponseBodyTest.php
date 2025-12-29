<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\SecurityContext;
use WebFiori\Tests\Http\TestUser;
use WebFiori\Tests\Http\TestServices\ResponseBodyTestService;
use WebFiori\Tests\Http\TestServices\MixedResponseService;
use WebFiori\Tests\Http\TestServices\LegacyService;

class ResponseBodyTest extends TestCase {
    
    protected function setUp(): void {
        SecurityContext::clear();
        // Clear any previous GET parameters
        unset($_GET['action']);
    }
    
    public function testArrayReturnValue() {
        $service = new ResponseBodyTestService();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        // Test method discovery - should find first GET method
        $targetMethod = $service->getTargetMethod();
        $this->assertEquals('getArrayData', $targetMethod);
        
        // Test ResponseBody annotation detection
        $this->assertTrue($service->hasResponseBodyAnnotation('getArrayData'));
        
        // Test return value processing
        $result = $service->getArrayData();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('users', $result);
    }
    
    public function testStringReturnValue() {
        $service = new ResponseBodyTestService();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        // Test specific method
        $this->assertTrue($service->hasResponseBodyAnnotation('getStringData'));
        
        $result = $service->getStringData();
        $this->assertIsString($result);
        $this->assertEquals('Resource created successfully', $result);
    }
    
    public function testNullReturnValue() {
        $service = new ResponseBodyTestService();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        // Test method discovery for POST
        $targetMethod = $service->getTargetMethod();
        $this->assertEquals('deleteData', $targetMethod);
        
        $result = $service->deleteData();
        $this->assertNull($result);
    }
    
    public function testObjectReturnValue() {
        $service = new ResponseBodyTestService();
        
        // Test specific method
        $this->assertTrue($service->hasResponseBodyAnnotation('getObjectData'));
        
        $result = $service->getObjectData();
        $this->assertIsObject($result);
        $this->assertTrue(property_exists($result, 'message', "The object should have the attribute 'message'."));
    }
    
    public function testMethodWithoutResponseBody() {
        $service = new ResponseBodyTestService();
        
        // Should not have ResponseBody annotation
        $this->assertFalse($service->hasResponseBodyAnnotation('getManualData'));
    }
    
    public function testMethodWithParameters() {
        $service = new ResponseBodyTestService();
        
        // Test that method has ResponseBody annotation
        $this->assertTrue($service->hasResponseBodyAnnotation('createUser'));
        
        // Test method has POST mapping
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $targetMethod = $service->getTargetMethod();
        $this->assertContains($targetMethod, ['deleteData', 'createUser']); // Either POST method
    }
    
    public function testMixedServiceWithAuthentication() {
        $service = new MixedResponseService();
        
        // Test methods have correct annotations
        $this->assertTrue($service->hasResponseBodyAnnotation('getSecureData'));
        $this->assertTrue($service->hasResponseBodyAnnotation('getPublicData'));
        $this->assertFalse($service->hasResponseBodyAnnotation('traditionalMethod'));
        
        // Test with authentication
        SecurityContext::setCurrentUser(new TestUser(1, ['USER']));
        
        // The service should be authorized since we set up proper authentication
        $this->assertTrue($service->checkMethodAuthorization());
    }
    
    public function testLegacyServiceCompatibility() {
        $service = new LegacyService();
        
        // Should find the GET method
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $targetMethod = $service->getTargetMethod();
        $this->assertEquals('getData', $targetMethod);
        
        // Method should not have ResponseBody annotation
        $this->assertFalse($service->hasResponseBodyAnnotation('getData'));
    }
    
    public function testProcessWithAutoHandling() {
        $service = new ResponseBodyTestService();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        // Test the auto-processing logic
        $targetMethod = $service->getTargetMethod();
        $hasResponseBody = $service->hasResponseBodyAnnotation($targetMethod);
        
        $this->assertTrue($hasResponseBody);
        $this->assertEquals('getArrayData', $targetMethod);
    }
    
    public function testResponseBodyAnnotationConfiguration() {
        $service = new ResponseBodyTestService();
        
        // Test default ResponseBody annotation
        $reflection = new \ReflectionMethod($service, 'getArrayData');
        $attributes = $reflection->getAttributes(\WebFiori\Http\Annotations\ResponseBody::class);
        $this->assertNotEmpty($attributes);
        
        $responseBody = $attributes[0]->newInstance();
        $this->assertEquals(200, $responseBody->status);
        $this->assertEquals('success', $responseBody->type);
        
        // Test custom ResponseBody annotation
        $reflection = new \ReflectionMethod($service, 'getStringData');
        $attributes = $reflection->getAttributes(\WebFiori\Http\Annotations\ResponseBody::class);
        $responseBody = $attributes[0]->newInstance();
        $this->assertEquals(201, $responseBody->status);
        $this->assertEquals('created', $responseBody->type);
    }
    
    protected function tearDown(): void {
        SecurityContext::clear();
        unset($_GET['action']);
    }
}
