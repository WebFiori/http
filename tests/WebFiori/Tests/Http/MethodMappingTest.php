<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\RequestMethod;
use WebFiori\Tests\Http\TestServices\MappedMethodsService;
use WebFiori\Tests\Http\TestServices\AllMethodsService;

class MethodMappingTest extends TestCase {
    
    public function testGetAndPostMapping() {
        $service = new MappedMethodsService();
        $methods = $service->getRequestMethods();
        
        $this->assertContains(RequestMethod::GET, $methods);
        $this->assertContains(RequestMethod::POST, $methods);
        $this->assertCount(2, $methods);
    }
    
    public function testAllMethodMappings() {
        $service = new AllMethodsService();
        $methods = $service->getRequestMethods();
        
        $this->assertContains(RequestMethod::GET, $methods);
        $this->assertContains(RequestMethod::POST, $methods);
        $this->assertContains(RequestMethod::PUT, $methods);
        $this->assertContains(RequestMethod::DELETE, $methods);
        $this->assertCount(4, $methods);
    }
    
    public function testServiceWithoutMethodAnnotations() {
        $service = new class extends \WebFiori\Http\WebService {
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $methods = $service->getRequestMethods();
        $this->assertEmpty($methods);
    }
    
    public function testMixedAnnotationAndManualConfiguration() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('mixed-service');
                $this->addRequestMethod(RequestMethod::PATCH); // Manual addition
            }
            
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getData() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $methods = $service->getRequestMethods();
        $this->assertContains(RequestMethod::GET, $methods); // From annotation
        $this->assertContains(RequestMethod::PATCH, $methods); // Manual addition
    }
}
