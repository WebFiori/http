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
        $action = new APIAction();
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
     */
    public function testConstructor02() {
        $action = new APIAction('do-something');
        $this->assertEquals('do-something',$action->getName());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $action = new APIAction('invalid name');
        $this->assertEquals('an-action',$action->getName());
    }
}
