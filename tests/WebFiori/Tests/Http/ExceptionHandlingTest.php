<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\Exceptions\NotFoundException;
use WebFiori\Http\Exceptions\BadRequestException;
use WebFiori\Http\Exceptions\UnauthorizedException;
use WebFiori\Http\Exceptions\ForbiddenException;
use WebFiori\Tests\Http\TestServices\ExceptionTestService;

class ExceptionHandlingTest extends TestCase {
    
    protected function setUp(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_GET['action']);
    }
    
    public function testHttpExceptionProperties() {
        $notFound = new NotFoundException('Resource not found');
        $this->assertEquals(404, $notFound->getStatusCode());
        $this->assertEquals('error', $notFound->getResponseType());
        $this->assertEquals('Resource not found', $notFound->getMessage());
        
        $badRequest = new BadRequestException('Invalid input');
        $this->assertEquals(400, $badRequest->getStatusCode());
        $this->assertEquals('error', $badRequest->getResponseType());
        
        $unauthorized = new UnauthorizedException('Login required');
        $this->assertEquals(401, $unauthorized->getStatusCode());
        $this->assertEquals('error', $unauthorized->getResponseType());
        
        $forbidden = new ForbiddenException('Access denied');
        $this->assertEquals(403, $forbidden->getStatusCode());
        $this->assertEquals('error', $forbidden->getResponseType());
    }
    
    public function testExceptionDefaults() {
        $notFound = new NotFoundException();
        $this->assertEquals('Not Found', $notFound->getMessage());
        
        $badRequest = new BadRequestException();
        $this->assertEquals('Bad Request', $badRequest->getMessage());
        
        $unauthorized = new UnauthorizedException();
        $this->assertEquals('Unauthorized', $unauthorized->getMessage());
        
        $forbidden = new ForbiddenException();
        $this->assertEquals('Forbidden', $forbidden->getMessage());
    }
    
    public function testServiceExceptionHandling() {
        $service = new ExceptionTestService();
        
        // Test that method has ResponseBody annotation
        $this->assertTrue($service->hasResponseBodyAnnotation('getUser'));
        
        // Test exception throwing with test parameter
        $_GET['test_id'] = 404;
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('User not found');
        $service->getUser();
    }
    
    public function testDifferentExceptionTypes() {
        $service = new ExceptionTestService();
        
        // Test BadRequestException
        $_GET['test_id'] = 400;
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid user ID');
        $service->getUser();
    }
    
    public function testUnauthorizedException() {
        $service = new ExceptionTestService();
        
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Authentication required');
        $service->createUser();
    }
    
    public function testGenericException() {
        $service = new ExceptionTestService();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Generic error');
        $service->getError();
    }
    
    public function testHandleExceptionMethod() {
        $service = new ExceptionTestService();
        $exception = new NotFoundException('Test not found');
        
        // Test the handleException method directly
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('handleException');
        
        // The method should not throw an exception
        $this->expectNotToPerformAssertions();
        $method->invoke($service, $exception);
    }
    
    protected function tearDown(): void {
        unset($_GET['action']);
        unset($_GET['test_id']);
        unset($_SERVER['REQUEST_METHOD']);
    }
}
