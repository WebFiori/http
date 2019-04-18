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
}
