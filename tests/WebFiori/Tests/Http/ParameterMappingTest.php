<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\ParamType;
use WebFiori\Tests\Http\TestServices\ParameterMappedService;

class ParameterMappingTest extends TestCase {
    
    public function testParametersFromAnnotations() {
        $service = new ParameterMappedService();
        $parameters = $service->getParameters();
        
        $this->assertCount(4, $parameters); // id, name, email, age
        
        // Check 'id' parameter
        $idParam = $service->getParameterByName('id');
        $this->assertNotNull($idParam);
        $this->assertEquals(ParamType::INT, $idParam->getType());
        $this->assertFalse($idParam->isOptional());
        $this->assertEquals('User ID', $idParam->getDescription());
        
        // Check 'name' parameter
        $nameParam = $service->getParameterByName('name');
        $this->assertNotNull($nameParam);
        $this->assertEquals(ParamType::STRING, $nameParam->getType());
        $this->assertTrue($nameParam->isOptional());
        $this->assertEquals('Anonymous', $nameParam->getDefault());
        
        // Check 'email' parameter
        $emailParam = $service->getParameterByName('email');
        $this->assertNotNull($emailParam);
        $this->assertEquals(ParamType::EMAIL, $emailParam->getType());
        $this->assertFalse($emailParam->isOptional());
        
        // Check 'age' parameter
        $ageParam = $service->getParameterByName('age');
        $this->assertNotNull($ageParam);
        $this->assertEquals(ParamType::INT, $ageParam->getType());
        $this->assertTrue($ageParam->isOptional());
        $this->assertEquals(18, $ageParam->getDefault());
    }
    
    public function testHttpMethodsFromParameterAnnotations() {
        $service = new ParameterMappedService();
        $methods = $service->getRequestMethods();
        
        $this->assertContains(\WebFiori\Http\RequestMethod::GET, $methods);
        $this->assertContains(\WebFiori\Http\RequestMethod::POST, $methods);
        $this->assertCount(2, $methods);
    }
}
