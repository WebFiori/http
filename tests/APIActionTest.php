<?php
namespace restEasy\tests;
use PHPUnit\Framework\TestCase;
use restEasy\RequestParameter;
use restEasy\APIAction;
class APIActionTest extends TestCase{
    /**
     * @test
     */
    public function testConstructor00() {
        $action = new APIAction('');
        $this->assertEquals('an-action',$action->getName());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $action = new APIAction('  ');
        $this->assertEquals('an-action',$action->getName());
    }
    /**
     * @test
     * @return APIAction
     */
    public function testConstructor02() {
        $action = new APIAction('get-user-info');
        $this->assertEquals('get-user-info',$action->getName());
        return $action;
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $action = new APIAction('invalid name');
        $this->assertEquals('an-action',$action->getName());
    }
    /**
     * @test
     * @depends testConstructor02
     * @param APIAction $action 
     */
    public function testAddRequestMethod00($action) {
        $this->assertTrue($action->addRequestMethod('get'));
        $this->assertFalse($action->addRequestMethod('get'));
        $this->assertFalse($action->addRequestMethod(' Get '));
        $this->assertTrue($action->addRequestMethod(' PoSt '));
        $this->assertTrue($action->addRequestMethod('   DeLete'));
        $this->assertTrue($action->addRequestMethod('   options'));
        $this->assertFalse($action->addRequestMethod(' Random meth'));
        $requestMethods = $action->getActionMethods();
        $this->assertEquals('GET',$requestMethods[0]);
        $this->assertEquals('POST',$requestMethods[1]);
        $this->assertEquals('DELETE',$requestMethods[2]);
        $this->assertEquals('OPTIONS',$requestMethods[3]);
        return $action;
    }
    /**
     * @test
     * @param APIAction $action
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
        $this->assertEquals(0,count($action->getActionMethods()));
    }
    /**
     * @test
     */
    public function testAddParameter00() {
        $action = new APIAction('add-user');
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
    public function testHasParameter00() {
        $action = new APIAction('add-user');
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
    public function testAddResponseDesc00() {
        $action = new APIAction('get-user');
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
    public function testToString00() {
        $action = new APIAction('get-user');
        $action->addRequestMethod('get');
        $action->addParameter(new RequestParameter('user-id', 'integer'));
        $action->getParameterByName('user-id')->setDescription('The ID of the user.');
        $action->setDescription('Returns a JSON string which holds user profile info.');
        $this->assertEquals("APIAction[\n"
                . "    Name => 'get-user',\n"
                . "    Description => 'Returns a JSON string which holds user profile info.',\n"
                . "    Since => 'null',\n"
                . "    Request Methods => [\n"
                . "        GET\n"
                . "    ],\n"
                . "    Parameters => [\n"
                . "        user-id => [\n"
                . "            Type => 'integer',\n"
                . "            Description => 'The ID of the user.',\n"
                . "            Is Optional => 'false',\n"
                . "            Default => 'null',\n"
                . "            Minimum Value => '-2147483648',\n"
                . "            Maximum Value => '2147483647'\n"
                . "        ]\n"
                . "    ],\n"
                . "    Responses Descriptions => [\n"
                . "    ]\n"
                . "]\n",$action.'');
    }
    /**
     * @test
     */
    public function testToString01() {
        $action = new APIAction('add-user');
        $action->addRequestMethod('post');
        $action->addRequestMethod('put');
        $action->addParameter(new RequestParameter('username'));
        $action->addParameter(new RequestParameter('email'));
        $action->getParameterByName('username')->setDescription('The username of the user.');
        $action->getParameterByName('email')->setDescription('The email address of the user.');
        $action->setDescription('Adds new user profile to the system.');
        $action->addResponseDescription('If the user is added, a 201 HTTP response is send with a JSON string that contains user ID.');
        $action->addResponseDescription('If a user is already exist wich has the given email, a 404 code is sent back.');
        print_r($action.'');
        $this->assertEquals("APIAction[\n"
                . "    Name => 'add-user',\n"
                . "    Description => 'Adds new user profile to the system.',\n"
                . "    Since => 'null',\n"
                . "    Request Methods => [\n"
                . "        POST,\n"
                . "        PUT\n"
                . "    ],\n"
                . "    Parameters => [\n"
                . "        username => [\n"
                . "            Type => 'string',\n"
                . "            Description => 'The username of the user.',\n"
                . "            Is Optional => 'false',\n"
                . "            Default => 'null',\n"
                . "            Minimum Value => 'null',\n"
                . "            Maximum Value => 'null'\n"
                . "        ],\n"
                . "        email => [\n"
                . "            Type => 'string',\n"
                . "            Description => 'The email address of the user.',\n"
                . "            Is Optional => 'false',\n"
                . "            Default => 'null',\n"
                . "            Minimum Value => 'null',\n"
                . "            Maximum Value => 'null'\n"
                . "        ]\n"
                . "    ],\n"
                . "    Responses Descriptions => [\n"
                . "        Response #0 => 'If the user is added, a 201 HTTP response is send with a JSON string that contains user ID.',\n"
                . "        Response #1 => 'If a user is already exist wich has the given email, a 404 code is sent back.'\n"
                . "    ]\n"
                . "]\n",$action.'');
    }
}
