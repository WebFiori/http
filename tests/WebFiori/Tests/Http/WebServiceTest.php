<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\RequestParameter;
use WebFiori\Tests\Http\TestServices\NoAuthService;
use WebFiori\Tests\Http\TestServices\TestServiceObj;

class WebServiceTest extends TestCase {
    /**
     * 
     */
    public function testGetAuthHeaders00() {
        $service = new TestServiceObj('Hello');
        $this->assertNull($service->getAuthHeader());
        $this->assertTrue($service->isAuthorized());
    }
    /**
     * 
     */
    public function testGetAuthHeaders01() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid authorization header structure.');
        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic';
        $service = new TestServiceObj('Hello');
        $this->assertNull($service->getAuthHeader());
    }
    /**
     * 
     */
    public function testGetAuthHeaders02() {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Basic XYZ';
        $service = new TestServiceObj('Hello');
        $header = $service->getAuthHeader();
        $this->assertNotNull($header);
        $this->assertEquals('basic', $header->getScheme());
        $this->assertEquals('XYZ', $header->getCredentials());
    }
    /**
     * @test
     */
    public function test00() {
        $service = new NoAuthService();
        $this->assertFalse($service->isAuthRequired());
    }
    /**
     * @test
     */
    public function testAddParameter00() {
        $action = new TestServiceObj('add-user');
        $rp00 = new RequestParameter('name');
        $this->assertTrue($action->addParameter($rp00));
        $rp01 = new RequestParameter('name');
        $this->assertFalse($action->addParameter($rp01));
        $rp02 = new RequestParameter('email-address');
        $this->assertTrue($action->addParameter($rp02));
        $this->assertFalse($action->addParameter(''));
        $this->assertEquals(2,count($action->getParameters()));
    }
    /**
     * @test
     */
    public function testAddParameter01() {
        $action = new TestServiceObj('add-user');
        $this->assertTrue($action->addParameter([
            ParamOption::NAME => 'new-param',
            ParamOption::TYPE => ParamType::BOOL
        ]));
        
        $this->assertEquals(1,count($action->getParameters()));
        $param = $action->getParameterByName('new-param');
        $this->assertEquals('boolean', $param->getType());
    }
    /**
     * @test
     */
    public function testAddParameters00() {
        $action = new TestServiceObj('add-user');
        $action->addParameters([
            'username' => []
        ]);
        $this->assertEquals(1,count($action->getParameters()));
        $param = $action->getParameterByName('username');
        $this->assertEquals('string', $param->getType());
    }
    /**
     * @test
     */
    public function testAddParameters01() {
        $action = new TestServiceObj('add-user');
        $action->addParameters([
            new RequestParameter('username')
        ]);
        $this->assertEquals(1,count($action->getParameters()));
        $param = $action->getParameterByName('username');
        $this->assertEquals('string', $param->getType());
    }
    /**
     * @test
     */
    public function testAddParameters02() {
        $action = new TestServiceObj('add-user');
        $action->addParameters([
            new RequestParameter('username'),
            'password' => [
                ParamOption::OPTIONAL => true,
                ParamOption::DEFAULT => 1234,
                ParamOption::TYPE => 'integer',
                ParamOption::METHODS => 'get'
            ]
        ]);
        $this->assertEquals(2,count($action->getParameters()));
        $param = $action->getParameterByName('username');
        $this->assertEquals('string', $param->getType());
        
        $param2 = $action->getParameterByName('password');
        $this->assertEquals('integer', $param2->getType());
        $this->assertTrue($param2->isOptional());
        $this->assertEquals(1234, $param2->getDefault());
        $this->assertEquals(['GET'], $param2->getMethods());
    }
    /**
     * @test
     * @depends testConstructor02
     * @param TestServiceObj $action 
     */
    public function testAddRequestMethod00($action) {
        $this->assertTrue($action->addRequestMethod('get'));
        $this->assertFalse($action->addRequestMethod('get'));
        $this->assertFalse($action->addRequestMethod(' Get '));
        $this->assertTrue($action->addRequestMethod(' PoSt '));
        $this->assertTrue($action->addRequestMethod('   DeLete'));
        $this->assertTrue($action->addRequestMethod('   options'));
        $this->assertFalse($action->addRequestMethod(' Random meth'));
        $requestMethods = $action->getRequestMethods();
        $this->assertEquals('GET',$requestMethods[0]);
        $this->assertEquals('POST',$requestMethods[1]);
        $this->assertEquals('DELETE',$requestMethods[2]);
        $this->assertEquals('OPTIONS',$requestMethods[3]);

        return $action;
    }
    /**
     * @test
     */
    public function testAddResponseDesc00() {
        $action = new TestServiceObj('get-user');
        $action->addResponseDescription('');
        $action->addResponseDescription('   ');
        $this->assertEquals(0,count($action->getResponsesDescriptions()));
        $action->addResponseDescription('Returns a JSON string which holds user profile info.');
        $this->assertEquals(1,count($action->getResponsesDescriptions()));
        $this->assertEquals('Returns a JSON string which holds user profile info.',$action->getResponsesDescriptions()[0]);
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $action = new TestServiceObj('');
        $this->assertEquals('new-service',$action->getName());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $action = new TestServiceObj('  ');
        $this->assertEquals('new-service',$action->getName());
    }
    /**
     * @test
     * @return TestServiceObj
     */
    public function testConstructor02() {
        $action = new TestServiceObj('get-user-info');
        $this->assertEquals('get-user-info',$action->getName());

        return $action;
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $action = new TestServiceObj('invalid name');
        $this->assertEquals('new-service',$action->getName());
    }
    /**
     * @test
     */
    public function testGetParameterByName00() {
        $action = new TestServiceObj('do-somthing');
        $this->assertNull($action->getParameterByName('     '));
        $this->assertNull($action->getParameterByName(''));
        $this->assertNull($action->getParameterByName('username'));
        $action->addParameter(new RequestParameter('username'));
        $this->assertTrue($action->getParameterByName('     username') instanceof RequestParameter);
        $action->addParameter(new RequestParameter('password'));
        $this->assertTrue($action->getParameterByName('     password') instanceof RequestParameter);
        $action->addParameter(new RequestParameter('email'));
        $this->assertTrue($action->getParameterByName('     email') instanceof RequestParameter);
        $action->removeParameter(' username');
        $this->assertNull($action->getParameterByName('username'));
    }
    /**
     * @test
     */
    public function testHasParameter00() {
        $action = new TestServiceObj('add-user');
        $this->assertFalse($action->hasParameter(''));
        $this->assertFalse($action->hasParameter('name'));
        $rp00 = new RequestParameter('name');
        $action->addParameter($rp00);
        $this->assertTrue($action->hasParameter(' name '));
        $this->assertFalse($action->hasParameter(' Name '));
        $action->removeParameter('name');
        $this->assertFalse($action->hasParameter('name'));
    }
    /**
     * @test
     */
    public function testRemoveParameter00() {
        $action = new TestServiceObj('hello');
        $action->addParameter(new RequestParameter('world'));
        $action->addParameter(new RequestParameter('ibrahim'));
        $action->addParameter(new RequestParameter('ali'));
        $this->assertEquals(3,count($action->getParameters()));
        $this->assertTrue($action->removeParameter('ibrahim') instanceof RequestParameter);
        $this->assertEquals(2,count($action->getParameters()));
        $this->assertNull($action->removeParameter('ibrahim'));
        $this->assertEquals(2,count($action->getParameters()));
        $this->assertTrue($action->removeParameter('ali') instanceof RequestParameter);
        $this->assertTrue($action->removeParameter('world') instanceof RequestParameter);
        $this->assertEquals(0,count($action->getParameters()));
    }
    /**
     * @test
     * @param TestServiceObj $action
     * @depends testAddRequestMethod00
     */
    public function testRemoveRequestMethod($action) {
        $this->assertTrue($action->removeRequestMethod('get'));
        $this->assertFalse($action->removeRequestMethod('get'));
        $this->assertTrue($action->removeRequestMethod(' PoSt '));
        $this->assertFalse($action->removeRequestMethod('post'));
        $this->assertFalse($action->removeRequestMethod('random'));
        $this->assertTrue($action->removeRequestMethod('options'));
        $this->assertTrue($action->removeRequestMethod('delete'));
        $this->assertEquals(0,count($action->getRequestMethods()));
    }
    /**
     * @test
     */
    public function testToJson00() {
        $action = new TestServiceObj('login');
        $this->assertEquals('{}',$action->toJSON().'');
        $action->setSince('1.0.1');
        $action->setDescription('Allow the user to login to the system.');
        $this->assertEquals('{}',$action->toJSON().'');
        $action->setRequestMethods([RequestMethod::GET, RequestMethod::POST, RequestMethod::PUT]);

        $this->assertEquals(''
                .'{"get":{"responses":{"200":{"description":"Successful operation"}}},'
                .'"post":{"responses":{"200":{"description":"Successful operation"}}},'
                .'"put":{"responses":{"200":{"description":"Successful operation"}}}}',$action->toJSON().'');
        $action->removeRequestMethod('put');
        $action->addParameter(new RequestParameter('username'));
        $this->assertEquals(''
                .'{"get":{"responses":{"200":{"description":"Successful operation"}}},'
                .'"post":{"responses":{"200":{"description":"Successful operation"}}}}',$action->toJSON().'');
        $action->addParameter(new RequestParameter('password', 'integer'));
        $action->getParameterByName('password')->setDescription('The password of the user.');
        $action->getParameterByName('password')->setMinValue(1000000);
        $this->assertEquals(''
                .'{"get":{"responses":{"200":{"description":"Successful operation"}}},'
                .'"post":{"responses":{"200":{"description":"Successful operation"}}}}',$action->toJSON().'');
    }
    /**
     * @test
     */
    public function testToString00() {
        $action = new TestServiceObj('get-user');
        $action->addRequestMethod(RequestMethod::GET);
        $action->addParameter(new RequestParameter('user-id', 'integer'));
        $action->getParameterByName('user-id')->setDescription('The ID of the user.');
        $action->setDescription('Returns a JSON string which holds user profile info.');
        $this->assertEquals('{"get":{"responses":{"200":{"description":"Successful operation"}}}}',$action.'');
    }
    /**
     * @test
     */
    public function testToString01() {
        $action = new TestServiceObj('add-user');
        $action->setRequestMethods([RequestMethod::POST, RequestMethod::PUT]);
        $action->addParameter(new RequestParameter('username'));
        $action->addParameter(new RequestParameter('email'));
        $action->getParameterByName('username')->setDescription('The username of the user.');
        $action->getParameterByName('email')->setDescription('The email address of the user.');
        $action->setDescription('Adds new user profile to the system.');
        $action->addResponse(RequestMethod::POST, '201', 'User created successfully');
        $action->addResponse(RequestMethod::PUT, '200', 'User updated successfully');
        $this->assertEquals('{"post":{"responses":{"201":{"description":"User created successfully"}}},'
                .'"put":{"responses":{"200":{"description":"User updated successfully"}}}}',$action.'');
    }
    /**
     * @test
     */
    public function testDuplicateGetMappingThrowsException() {
        $this->expectException(\WebFiori\Http\Exceptions\DuplicateMappingException::class);
        $this->expectExceptionMessage('HTTP method GET is mapped to multiple methods: getUsers, getUsersAgain');
        
        new class extends \WebFiori\Http\WebService {
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getUsers() {}
            
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getUsersAgain() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
    }

    /**
     * @test
     */
    public function testDuplicatePostMappingThrowsException() {
        $this->expectException(\WebFiori\Http\Exceptions\DuplicateMappingException::class);
        $this->expectExceptionMessage('HTTP method POST is mapped to multiple methods: createUser, addUser');
        
        new class extends \WebFiori\Http\WebService {
            #[\WebFiori\Http\Annotations\PostMapping]
            public function createUser() {}
            
            #[\WebFiori\Http\Annotations\PostMapping]
            public function addUser() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
    }

    /**
     * @test
     */
    public function testDuplicateMixedMappingsThrowsException() {
        $this->expectException(\WebFiori\Http\Exceptions\DuplicateMappingException::class);
        $this->expectExceptionMessage('HTTP method PUT is mapped to multiple methods: updateUser, modifyUser');
        
        new class extends \WebFiori\Http\WebService {
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getUser() {}
            
            #[\WebFiori\Http\Annotations\PutMapping]
            public function updateUser() {}
            
            #[\WebFiori\Http\Annotations\PutMapping]
            public function modifyUser() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
    }

    /**
     * @test
     */
    public function testMultipleDuplicateGetMappingThrowsException() {
        $this->expectException(\WebFiori\Http\Exceptions\DuplicateMappingException::class);
        $this->expectExceptionMessage('HTTP method GET is mapped to multiple methods: getUsers, getUsersAgain, fetchUsers');
        
        new class extends \WebFiori\Http\WebService {
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getUsers() {}
            
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getUsersAgain() {}
            
            #[\WebFiori\Http\Annotations\GetMapping]
            public function fetchUsers() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
    }

    /**
     * @test
     */
    public function testNoDuplicateMappingsDoesNotThrowException() {
        $service = new class extends \WebFiori\Http\WebService {
            #[\WebFiori\Http\Annotations\GetMapping]
            public function getUser() {}
            
            #[\WebFiori\Http\Annotations\PostMapping]
            public function createUser() {}
            
            #[\WebFiori\Http\Annotations\PutMapping]
            public function updateUser() {}
            
            #[\WebFiori\Http\Annotations\DeleteMapping]
            public function deleteUser() {}
            
            public function isAuthorized(): bool { return true; }
            public function processRequest() {}
        };
        
        $methods = $service->getRequestMethods();
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
        $this->assertContains('PUT', $methods);
        $this->assertContains('DELETE', $methods);
        $this->assertCount(4, $methods);
    }
    
    public function testConflictingAnnotations() {
        $service = new \WebFiori\Tests\Http\TestServices\ConflictingAnnotationsService();
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('conflicting annotations');
        
        $service->checkMethodAuthorization();
    }
}





