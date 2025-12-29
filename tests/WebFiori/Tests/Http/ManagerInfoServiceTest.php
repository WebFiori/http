<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\ManagerInfoService;
use WebFiori\Http\WebServicesManager;
use WebFiori\Http\RequestMethod;

class ManagerInfoServiceTest extends TestCase {
    
    /**
     * @test
     */
    public function testManagerInfoService() {
        $manager = new WebServicesManager();
        $service = new TestManagerInfoService();
        $manager->addService($service);
        
        $this->assertEquals('api-docs', $service->getName());
        $this->assertStringContainsString('information about all end points', $service->getDescription());
        $this->assertContains(RequestMethod::GET, $service->getRequestMethods());
        
        // Test that processRequest sends JSON
        $this->assertNotNull($service->getManager());
        $this->assertSame($manager, $service->getManager());
    }
}

class TestManagerInfoService extends ManagerInfoService {
    public function isAuthorized(): bool {
        return true;
    }
}
