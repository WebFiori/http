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
    public function testConstructor07() {
        $requestParam = new RequestParameter('valid',' floaT ',true);
        $this->assertEquals('valid',$requestParam->getName());
        $this->assertEquals('float',$requestParam->getType());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertTrue($requestParam->isOptional());
        if(PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 2){
            $this->assertEquals(PHP_FLOAT_MAX, $requestParam->getMaxVal());
            $this->assertEquals(PHP_FLOAT_MIN,$requestParam->getMinVal());
        }
        else{
            $this->assertEquals(PHP_INT_MAX, $requestParam->getMaxVal());
            $this->assertEquals(~PHP_INT_MAX,$requestParam->getMinVal());
        }
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('float',$requestParam->getType());
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
    /**
     * @test
     */
    public function testSetMax00() {
        $rp = new RequestParameter('val');
        $this->assertFalse($rp->setMaxVal(5));
        $this->assertNull($rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax01() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMaxVal(5));
        $this->assertEquals(5,$rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax02() {
        $rp = new RequestParameter('val','integer');
        $this->assertFalse($rp->setMaxVal('5'));
        $this->assertEquals(PHP_INT_MAX,$rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax03() {
        $rp = new RequestParameter('val','integer');
        $this->assertFalse($rp->setMaxVal(66.90));
        $this->assertEquals(PHP_INT_MAX,$rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax04() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('float',$rp->getType());
        $this->assertTrue($rp->setMaxVal(5.6));
        $this->assertEquals(5.6,$rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax05() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('float',$rp->getType());
        $this->assertFalse($rp->setMaxVal('5'));
        if(PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 2){
            $this->assertEquals(PHP_FLOAT_MAX,$rp->getMaxVal());
        }
        else{
            $this->assertEquals(PHP_INT_MAX,$rp->getMaxVal());
        }
    }
    /**
     * @test
     */
    public function testSetMax06() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('float',$rp->getType());
        $this->assertTrue($rp->setMaxVal(66));
        $this->assertEquals(66,$rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax07() {
        $rp = new RequestParameter('val','integer');
        $rp->setMinVal(0);
        $this->assertFalse($rp->setMaxVal(-66));
        $this->assertEquals(PHP_INT_MAX,$rp->getMaxVal());
        $this->assertFalse($rp->setMaxVal(0));
        $this->assertTrue($rp->setMaxVal(1));
        $this->assertEquals(1,$rp->getMaxVal());
    }
}







