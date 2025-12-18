<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\WebServicesManager;
use WebFiori\Http\ParamType;

class FullAnnotationIntegrationTest extends TestCase {
    
    public function testCompleteAnnotationSystem() {
        $service = new class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct();
            }
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        // Test that annotation system doesn't interfere with manual configuration
        $service->setName('manual-service');
        $service->addRequestMethod(\WebFiori\Http\RequestMethod::GET);
        $service->addParameters([
            'test' => [
                \WebFiori\Http\ParamOption::TYPE => ParamType::STRING
            ]
        ]);
        
        $this->assertEquals('manual-service', $service->getName());
        $this->assertContains(\WebFiori\Http\RequestMethod::GET, $service->getRequestMethods());
        $this->assertNotNull($service->getParameterByName('test'));
    }
    
    public function testAnnotationOverridesManualConfiguration() {
        $service = new #[\WebFiori\Http\Annotations\RestController('annotated-override')] 
        class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct('manual-name'); // This should be overridden
            }
            
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getData() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $this->assertEquals('annotated-override', $service->getName());
        $this->assertContains(\WebFiori\Http\RequestMethod::GET, $service->getRequestMethods());
    }
    
    public function testMixedConfigurationApproach() {
        $service = new #[\WebFiori\Http\Annotations\RestController('mixed-service')] 
        class extends \WebFiori\Http\WebService {
            public function __construct() {
                parent::__construct();
                // Add manual configuration after annotation processing
                $this->addRequestMethod(\WebFiori\Http\RequestMethod::PATCH);
                $this->addParameters([
                    'manual_param' => [
                        \WebFiori\Http\ParamOption::TYPE => ParamType::STRING
                    ]
                ]);
            }
            
            #[\WebFiori\Http\Annotations\PostMapping]
            #[\WebFiori\Http\Annotations\RequestParam('annotated_param', 'int')]
            public function createData() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $this->assertEquals('mixed-service', $service->getName());
        
        $methods = $service->getRequestMethods();
        $this->assertContains(\WebFiori\Http\RequestMethod::POST, $methods); // From annotation
        $this->assertContains(\WebFiori\Http\RequestMethod::PATCH, $methods); // Manual addition
        
        $this->assertNotNull($service->getParameterByName('annotated_param')); // From annotation
        $this->assertNotNull($service->getParameterByName('manual_param')); // Manual addition
    }
}
