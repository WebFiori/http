<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\APITestCase;
use WebFiori\Http\SecurityContext;
use WebFiori\Http\WebServicesManager;
use WebFiori\Tests\Http\TestServices\AnnotatedService;
use WebFiori\Tests\Http\TestServices\NonAnnotatedService;

class RestControllerTest extends APITestCase {
    
    public function testAnnotatedServiceName() {
        $service = new AnnotatedService();
        $this->assertEquals('annotated-service', $service->getName());
        $this->assertEquals('A service configured via annotations', $service->getDescription());
        $this->assertEquals(['GET', 'DELETE'], $service->getRequestMethods());
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
        };
        
        $this->assertEquals('fallback-name', $service->getName());
    }
    
    public function testAnnotationWithoutFallback() {
        $service = new class extends \WebFiori\Http\WebService {
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

        $this->assertEquals('{'.self::NL
                . '    "message":"Method Not Allowed.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":405'.self::NL
                . '}', $this->postRequest($manager, 'annotated-service'));

        $this->assertEquals('{'.self::NL
                . '    "message":"Hi user!",'.self::NL
                . '    "type":"success",'.self::NL
                . '    "http-code":200'.self::NL
                . '}', $this->getRequest($manager, 'annotated-service'));

        $this->assertEquals('{'.self::NL
                . '    "message":"Hi Ibrahim!",'.self::NL
                . '    "type":"success",'.self::NL
                . '    "http-code":200'.self::NL
                . '}', $this->getRequest($manager, 'annotated-service', [
                    'name' => 'Ibrahim'
                ]));
    }
    public function testAnnotatedServiceMethodNotAllowed() {
        $manager = new WebServicesManager();
        $service = new AnnotatedService();
        $manager->addService($service);

        $this->assertEquals('{'.self::NL
                . '    "message":"Method Not Allowed.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":405'.self::NL
                . '}', $this->postRequest($manager, 'annotated-service'));

    }
    public function testAnnotatedGet() {
        $manager = new WebServicesManager();
        $service = new AnnotatedService();
        $manager->addService($service);
        
        $retrievedService = $manager->getServiceByName('annotated-service');
        $this->assertNotNull($retrievedService);
        $this->assertEquals('A service configured via annotations', $retrievedService->getDescription());

        $this->assertEquals('{'.self::NL
                . '    "message":"Hi user!",'.self::NL
                . '    "type":"success",'.self::NL
                . '    "http-code":200'.self::NL
                . '}', $this->getRequest($manager, 'annotated-service'));

        $this->assertEquals('{'.self::NL
                . '    "message":"Hi Ibrahim!",'.self::NL
                . '    "type":"success",'.self::NL
                . '    "http-code":200'.self::NL
                . '}', $this->getRequest($manager, 'annotated-service', [
                    'name' => 'Ibrahim'
                ]));
    }
    public function testAnnotatedDelete() {
        $manager = new WebServicesManager();
        $service = new AnnotatedService();
        $manager->addService($service);

        $this->assertEquals('{'.self::NL
                . '    "message":"The following required parameter(s) where missing from the request body: \'id\'.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":404,'.self::NL
                . '    "more-info":{'.self::NL
                . '        "missing":['.self::NL
                . '            "id"'.self::NL
                . '        ]'.self::NL
                . '    }'.self::NL
                . '}', $this->deleteRequest($manager, 'annotated-service'));

        $this->assertEquals('{'.self::NL
                . '    "message":"Not Authorized.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":401'.self::NL
                . '}', $this->deleteRequest($manager, 'annotated-service', [
                    'id' => 1
                ]));

        SecurityContext::setCurrentUser(['id' => 1, 'name' => 'Ibrahim']);
        $this->assertEquals('{'.self::NL
                . '    "message":"Not Authorized.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":401'.self::NL
                . '}', $this->deleteRequest($manager, 'annotated-service', [
                    'id' => 1
                ]));
        SecurityContext::setRoles(['ADMIN']);
        $this->assertEquals('{'.self::NL
                . '    "message":"Not Authorized.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":401'.self::NL
                . '}', $this->deleteRequest($manager, 'annotated-service', [
                    'id' => 1
                ]));
        SecurityContext::setAuthorities(['USER_DELETE']);
        $this->assertEquals('{'.self::NL
                . '    "message":"Delete user with ID: 1",'.self::NL
                . '    "type":"success",'.self::NL
                . '    "http-code":200'.self::NL
                . '}', $this->deleteRequest($manager, 'annotated-service', [
                    'id' => 1
                ]));
    }
}
