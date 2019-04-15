<?php
namespace restEasy\tests;
use PHPUnit\Framework\TestCase;
use restEasy\RequestParameter;
/**
 * Description of RequestParameterTest
 *
 * @author Eng.Ibrahim
 */
class RequestParameterTest extends TestCase{
    /**
     * @test
     */
    public function testConstructor00() {
        $requestParam = new RequestParameter('');
        $this->assertEquals('a-parameter',$requestParam->getName());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertFalse($requestParam->isOptional());
        $this->assertNull($requestParam->getMaxVal());
        $this->assertNull($requestParam->getMinVal());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('string',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $requestParam = new RequestParameter('invalid name');
        $this->assertEquals('a-parameter',$requestParam->getName());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertFalse($requestParam->isOptional());
        $this->assertNull($requestParam->getMaxVal());
        $this->assertNull($requestParam->getMinVal());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('string',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $requestParam = new RequestParameter('   valid-name-1-2-BUT-not-trimmed    ');
        $this->assertEquals('valid-name-1-2-BUT-not-trimmed',$requestParam->getName());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertFalse($requestParam->isOptional());
        $this->assertNull($requestParam->getMaxVal());
        $this->assertNull($requestParam->getMinVal());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('string',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $requestParam = new RequestParameter('valid','integer',true);
        $this->assertEquals('valid',$requestParam->getName());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertTrue($requestParam->isOptional());
        $this->assertEquals(PHP_INT_MAX, $requestParam->getMaxVal());
        $this->assertEquals(~PHP_INT_MAX,$requestParam->getMinVal());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('integer',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testConstructor04() {
        $requestParam = new RequestParameter('valid',' INteger ',true);
        $this->assertEquals('valid',$requestParam->getName());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertTrue($requestParam->isOptional());
        $this->assertEquals(PHP_INT_MAX, $requestParam->getMaxVal());
        $this->assertEquals(~PHP_INT_MAX,$requestParam->getMinVal());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('integer',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testConstructor06() {
        $requestParam = new RequestParameter('hello','unkown');
        $this->assertEquals('hello',$requestParam->getName());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertFalse($requestParam->isOptional());
        $this->assertNull($requestParam->getMaxVal());
        $this->assertNull($requestParam->getMinVal());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('string',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testSetIsEmptyStrAllowed00() {
        $requestParam = new RequestParameter('hello');
        $this->assertTrue($requestParam->setIsEmptyStringAllowed(true));
        $this->assertTrue($requestParam->isEmptyStringAllowed());
        $this->assertTrue($requestParam->setIsEmptyStringAllowed(false));
        $this->assertFalse($requestParam->isEmptyStringAllowed());
    }
    /**
     * @test
     */
    public function testSetIsEmptyStrAllowed01() {
        $requestParam = new RequestParameter('hello','integer');
        $this->assertFalse($requestParam->setIsEmptyStringAllowed(true));
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $requestParam->setType('string');
        $this->assertTrue($requestParam->setIsEmptyStringAllowed(true));
        $this->assertTrue($requestParam->isEmptyStringAllowed());
        $requestParam->setType('integer');
        $this->assertFalse($requestParam->setIsEmptyStringAllowed(false));
        $this->assertTrue($requestParam->isEmptyStringAllowed());
    }
}
