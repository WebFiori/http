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
        $this->assertNull($service->isAuthorized());
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
        $this->assertEquals(''
                .'{"name":"login",'
                .'"since":"1.0.0",'
                .'"description":"",'
                .'"request-methods":[],'
                .'"parameters":[],'
                .'"responses":[]}',$action->toJSON().'');
        $action->setSince('1.0.1');
        $action->setDescription('Allow the user to login to the system.');
        $this->assertEquals(''
                .'{"name":"login",'
                .'"since":"1.0.1",'
                .'"description":"Allow the user to login to the system.",'
                .'"request-methods":[],'
                .'"parameters":[],'
                .'"responses":[]}',$action->toJSON().'');
        $action->setRequestMethods([RequestMethod::GET, RequestMethod::POST, RequestMethod::PUT]);

        $this->assertEquals(''
                .'{"name":"login",'
                .'"since":"1.0.1",'
                .'"description":"Allow the user to login to the system.",'
                .'"request-methods":["GET","POST","PUT"],'
                .'"parameters":[],'
                .'"responses":[]}',$action->toJSON().'');
        $action->removeRequestMethod('put');
        $action->addParameter(new RequestParameter('username'));
        $this->assertEquals(''
                .'{"name":"login",'
                .'"since":"1.0.1",'
                .'"description":"Allow the user to login to the system.",'
                .'"request-methods":["GET","POST"],'
                .'"parameters":['
                .'{"name":"username",'
                .'"type":"string",'
                .'"description":null,'
                .'"is-optional":false,'
                .'"default-value":null,'
                .'"min-val":null,'
                .'"max-val":null,'
                .'"min-length":null,'
                .'"max-length":null}'
                .'],'
                .'"responses":[]}',$action->toJSON().'');
        $action->addParameter(new RequestParameter('password', 'integer'));
        $action->getParameterByName('password')->setDescription('The password of the user.');
        $action->getParameterByName('password')->setMinValue(1000000);
        $this->assertEquals(''
                .'{"name":"login",'
                .'"since":"1.0.1",'
                .'"description":"Allow the user to login to the system.",'
                .'"request-methods":["GET","POST"],'
                .'"parameters":['
                .'{"name":"username",'
                .'"type":"string",'
                .'"description":null,'
                .'"is-optional":false,'
                .'"default-value":null,'
                .'"min-val":null,'
                .'"max-val":null,'
                .'"min-length":null,'
                .'"max-length":null},'
                .'{"name":"password",'
                .'"type":"integer",'
                .'"description":"The password of the user.",'
                .'"is-optional":false,'
                .'"default-value":null,'
                .'"min-val":1000000,'
                .'"max-val":'.PHP_INT_MAX.','
                .'"min-length":null,'
                .'"max-length":null}'
                .'],'
                .'"responses":[]}',$action->toJSON().'');
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
        $this->assertEquals("APIAction[\n"
                ."    Name => 'get-user',\n"
                ."    Description => 'Returns a JSON string which holds user profile info.',\n"
                ."    Since => '1.0.0',\n"
                ."    Request Methods => [\n"
                ."        GET\n"
                ."    ],\n"
                ."    Parameters => [\n"
                ."        user-id => [\n"
                ."            Type => 'integer',\n"
                ."            Description => 'The ID of the user.',\n"
                ."            Is Optional => 'false',\n"
                ."            Default => 'null',\n"
                ."            Minimum Value => '".~PHP_INT_MAX."',\n"
                ."            Maximum Value => '".PHP_INT_MAX."'\n"
                ."        ]\n"
                ."    ],\n"
                ."    Responses Descriptions => [\n"
                ."    ]\n"
                ."]\n",$action.'');
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
        $action->addResponseDescription('If the user is added, a 201 HTTP response is send with a JSON string that contains user ID.');
        $action->addResponseDescription('If a user is already exist wich has the given email, a 404 code is sent back.');
        $this->assertEquals("APIAction[\n"
                ."    Name => 'add-user',\n"
                ."    Description => 'Adds new user profile to the system.',\n"
                ."    Since => '1.0.0',\n"
                ."    Request Methods => [\n"
                ."        POST,\n"
                ."        PUT\n"
                ."    ],\n"
                ."    Parameters => [\n"
                ."        username => [\n"
                ."            Type => 'string',\n"
                ."            Description => 'The username of the user.',\n"
                ."            Is Optional => 'false',\n"
                ."            Default => 'null',\n"
                ."            Minimum Value => 'null',\n"
                ."            Maximum Value => 'null'\n"
                ."        ],\n"
                ."        email => [\n"
                ."            Type => 'string',\n"
                ."            Description => 'The email address of the user.',\n"
                ."            Is Optional => 'false',\n"
                ."            Default => 'null',\n"
                ."            Minimum Value => 'null',\n"
                ."            Maximum Value => 'null'\n"
                ."        ]\n"
                ."    ],\n"
                ."    Responses Descriptions => [\n"
                ."        Response #0 => 'If the user is added, a 201 HTTP response is send with a JSON string that contains user ID.',\n"
                ."        Response #1 => 'If a user is already exist wich has the given email, a 404 code is sent back.'\n"
                ."    ]\n"
                ."]\n",$action.'');
    }
}
