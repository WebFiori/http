<?php
namespace restEasy\tests;

use PHPUnit\Framework\TestCase;
use webfiori\restEasy\RequestParameter;
/**
 * Description of RequestParameterTest
 *
 * @author Eng.Ibrahim
 */
class RequestParameterTest extends TestCase {
    /**
     * @test
     */
    public function testCreateParameter00() {
        $param = RequestParameter::createParam([]);
        $this->assertNull($param);
    }
    /**
     * @test
     */
    public function testCreateParameter01() {
        $param = RequestParameter::createParam([
            'name'=>'invalid name'
        ]);
        $this->assertNotNull($param);
        $this->assertEquals('a-parameter', $param->getName());
    }
    /**
     * @test
     */
    public function testCreateParameter02() {
        $param = RequestParameter::createParam([
            'name'=>'hello',
            'type' => 'integer',
            'min' => 33,
            'max' => 100,
            'custom-filter' => function ($original, $basicFilterResult, $param) {
                if ($basicFilterResult != \restEasy\APIFilter::INVALID) {
                    return $basicFilterResult * 100;
                }
            }
        ]);
        $this->assertNotNull($param);
        $this->assertEquals('hello', $param->getName());
        $this->assertEquals('integer', $param->getType());
        $this->assertEquals(33, $param->getMinVal());
        $this->assertEquals(100, $param->getMaxVal());
        $this->assertTrue(is_callable($param->getCustomFilterFunction()));
    }
    /**
     * @test
     */
    public function testCreateParameter03() {
        $param = RequestParameter::createParam([
            'name'=>'ok',
            'type' => 'string',
            'default' => 'Ibrahim',
            'allow-empty' => true,
            'description' => 'Super param.'
        ]);
        $this->assertNotNull($param);
        $this->assertEquals('ok', $param->getName());
        $this->assertEquals('string', $param->getType());
        $this->assertEquals('Ibrahim', $param->getDefault());
        $this->assertTrue($param->isEmptyStringAllowed());
        $this->assertEquals('Super param.', $param->getDescription());
    }
    /**
     * @test
     */
    public function testCreateParameter04() {
        $param = RequestParameter::createParam([
            'name'=>'ok',
            'type' => 'int',
            'default' => 44,
            'description' => 'Super param.'
        ]);
        $this->assertNotNull($param);
        $this->assertEquals('ok', $param->getName());
        $this->assertEquals('integer', $param->getType());
        $this->assertEquals(44, $param->getDefault());
        $this->assertEquals('Super param.', $param->getDescription());
    }
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

        return $requestParam;
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

        return $requestParam;
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
        $this->assertEquals('double',$requestParam->getType());
        $this->assertFalse($requestParam->isEmptyStringAllowed());
        $this->assertTrue($requestParam->isOptional());

        if (PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 2) {
            $this->assertEquals(PHP_FLOAT_MAX, $requestParam->getMaxVal());
            $this->assertEquals(PHP_FLOAT_MIN,$requestParam->getMinVal());
        } else {
            $this->assertEquals(PHP_INT_MAX, $requestParam->getMaxVal());
            $this->assertEquals(~PHP_INT_MAX,$requestParam->getMinVal());
        }
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('double',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testSetCustomFilter00() {
        $rp = new RequestParameter('hello');
        $this->assertNull($rp->getCustomFilterFunction());
        $this->assertFalse($rp->setCustomFilterFunction('not a func',false));
        $this->assertNull($rp->getCustomFilterFunction());
        $this->assertTrue($rp->applyBasicFilter());
    }
    /**
     * @test
     */
    public function testSetCustomFilter01() {
        $rp = new RequestParameter('hello');
        $this->assertTrue($rp->setCustomFilterFunction(function()
        {
        },false));
        $this->assertTrue(is_callable($rp->getCustomFilterFunction()));
        $this->assertFalse($rp->applyBasicFilter());
    }
    /**
     * @test
     */
    public function testSetCustomFilter02() {
        $rp = new RequestParameter('hello');
        $this->assertTrue($rp->setCustomFilterFunction(function()
        {
        },true));
        $this->assertTrue(is_callable($rp->getCustomFilterFunction()));
        $this->assertTrue($rp->applyBasicFilter());
    }
    /**
     * @test
     */
    public function testSetDefault00() {
        $rp = new RequestParameter('test','string');
        $this->assertTrue($rp->setDefault('A string.'));
        $this->assertEquals('A string.',$rp->getDefault());
        $this->assertFalse($rp->setDefault(44));
        $this->assertEquals('A string.',$rp->getDefault());
        $this->assertFalse($rp->setDefault(44.99));
        $this->assertFalse($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertFalse($rp->setDefault(false));
    }
    /**
     * @test
     */
    public function testSetDefault01() {
        $rp = new RequestParameter('test','url');
        $this->assertTrue($rp->setDefault('A string.'));
        $this->assertEquals('A string.',$rp->getDefault());
        $this->assertFalse($rp->setDefault(44));
        $this->assertEquals('A string.',$rp->getDefault());
        $this->assertFalse($rp->setDefault(44.99));
        $this->assertFalse($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertFalse($rp->setDefault(false));
    }
    /**
     * @test
     */
    public function testSetDefault02() {
        $rp = new RequestParameter('test','email');
        $this->assertTrue($rp->setDefault('A string.'));
        $this->assertEquals('A string.',$rp->getDefault());
        $this->assertFalse($rp->setDefault(44));
        $this->assertEquals('A string.',$rp->getDefault());
        $this->assertFalse($rp->setDefault(44.99));
        $this->assertFalse($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertFalse($rp->setDefault(false));
    }
    /**
     * @test
     */
    public function testSetDefault03() {
        $rp = new RequestParameter('test','integer');
        $this->assertFalse($rp->setDefault('A string.'));
        $this->assertTrue($rp->setDefault(44));
        $this->assertEquals(44,$rp->getDefault());
        $this->assertFalse($rp->setDefault(44.99));
        $this->assertFalse($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertFalse($rp->setDefault(false));
    }
    /**
     * @test
     */
    public function testSetDefault04() {
        $rp = new RequestParameter('test','float');
        $this->assertFalse($rp->setDefault('A string.'));
        $this->assertTrue($rp->setDefault(44));
        $this->assertTrue($rp->setDefault(44.99));
        $this->assertFalse($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertFalse($rp->setDefault(false));
    }
    /**
     * @test
     */
    public function testSetDefault05() {
        $rp = new RequestParameter('test','boolean');
        $this->assertFalse($rp->setDefault('A string.'));
        $this->assertFalse($rp->setDefault(44));
        $this->assertFalse($rp->setDefault(44.99));
        $this->assertFalse($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertTrue($rp->setDefault(false));
    }
    /**
     * @test
     */
    public function testSetDefault06() {
        $rp = new RequestParameter('test','array');
        $this->assertFalse($rp->setDefault('A string.'));
        $this->assertFalse($rp->setDefault(44));
        $this->assertFalse($rp->setDefault(44.99));
        $this->assertTrue($rp->setDefault([]));
        $this->assertFalse($rp->setDefault(new \Exception()));
        $this->assertFalse($rp->setDefault(null));
        $this->assertFalse($rp->setDefault(false));
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
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMaxVal(5.6));
        $this->assertEquals(5.6,$rp->getMaxVal());
    }
    /**
     * @test
     */
    public function testSetMax05() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertFalse($rp->setMaxVal('5'));

        if (PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 2) {
            $this->assertEquals(PHP_FLOAT_MAX,$rp->getMaxVal());
        } else {
            $this->assertEquals(PHP_INT_MAX,$rp->getMaxVal());
        }
    }
    /**
     * @test
     */
    public function testSetMax06() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
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
    /**
     * @test
     */
    public function testSetMin00() {
        $rp = new RequestParameter('val');
        $this->assertFalse($rp->setMinVal(5));
        $this->assertNull($rp->getMinVal());
    }
    /**
     * @test
     */
    public function testSetMin01() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMinVal(5));
        $this->assertEquals(5,$rp->getMinVal());
    }
    /**
     * @test
     */
    public function testSetMin02() {
        $rp = new RequestParameter('val','integer');
        $this->assertFalse($rp->setMinVal('5'));
        $this->assertEquals(~PHP_INT_MAX,$rp->getMinVal());
    }
    /**
     * @test
     */
    public function testSetMin03() {
        $rp = new RequestParameter('val','integer');
        $this->assertFalse($rp->setMinVal(66.90));
        $this->assertEquals(~PHP_INT_MAX,$rp->getMinVal());
    }
    /**
     * @test
     */
    public function testSetMin04() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMinVal(5.6));
        $this->assertEquals(5.6,$rp->getMinVal());
    }
    /**
     * @test
     */
    public function testSetMin05() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertFalse($rp->setMinVal('5'));

        if (PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >= 2) {
            $this->assertEquals(PHP_FLOAT_MIN,$rp->getMinVal());
        } else {
            $this->assertEquals(~PHP_INT_MAX,$rp->getMinVal());
        }
    }
    /**
     * @test
     */
    public function testSetMin06() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMinVal(66));
        $this->assertEquals(66,$rp->getMinVal());
    }
    /**
     * @test
     */
    public function testSetMin07() {
        $rp = new RequestParameter('val','integer');
        $rp->setMaxVal(-100);
        $this->assertFalse($rp->setMinVal(66));
        $this->assertEquals(~PHP_INT_MAX,$rp->getMinVal());
        $this->assertFalse($rp->setMinVal(-100));
        $this->assertTrue($rp->setMinVal(-101));
        $this->assertEquals(-101,$rp->getMinVal());
    }
    /**
     * @test
     * @depends testConstructor00
     * @param RequestParameter $reqParam
     */
    public function testToJson00($reqParam) {
        $reqParam->setDescription('Test Parameter.');
        $this->assertEquals('{"name":"a-parameter", "type":"string", "description":"Test Parameter.", '
                .'"is-optional":false, "default-value":null, "min-val":null, "max-val":null}',$reqParam->toJSON().'');
    }
    /**
     * @test
     * @depends testConstructor03
     * @param RequestParameter $reqParam
     */
    public function testToJson01($reqParam) {
        $reqParam->setDescription('Test Parameter.');
        $this->assertEquals('{"name":"valid", "type":"integer", "description":"Test Parameter.", '
                .'"is-optional":true, "default-value":null, "min-val":'.~PHP_INT_MAX.', "max-val":'.PHP_INT_MAX.'}',$reqParam->toJSON().'');
    }
    /**
     * 
     * @test
     */
    public function testToString00() {
        $rp = new RequestParameter('');
        $this->assertEquals("RequestParameter[\n"
                ."    Name => 'a-parameter',\n"
                ."    Type => 'string',\n"
                ."    Description => 'null',\n"
                ."    Is Optional => 'false',\n"
                ."    Default => 'null',\n"
                ."    Minimum Value => 'null',\n"
                ."    Maximum Value => 'null'\n"
                ."]\n",$rp.'');
    }
    /**
     * 
     * @test
     */
    public function testToString01() {
        $rp = new RequestParameter('user-id','integer');
        $rp->setMinVal(0);
        $rp->setMaxVal(1000);
        $rp->setDescription(' The ID of the user. ');
        $this->assertEquals("RequestParameter[\n"
                ."    Name => 'user-id',\n"
                ."    Type => 'integer',\n"
                ."    Description => 'The ID of the user.',\n"
                ."    Is Optional => 'false',\n"
                ."    Default => 'null',\n"
                ."    Minimum Value => '0',\n"
                ."    Maximum Value => '1000'\n"
                ."]\n",$rp.'');
        $rp->setDefault(33);
        $rp->setIsOptional(true);
        $this->assertEquals("RequestParameter[\n"
                ."    Name => 'user-id',\n"
                ."    Type => 'integer',\n"
                ."    Description => 'The ID of the user.',\n"
                ."    Is Optional => 'true',\n"
                ."    Default => '33',\n"
                ."    Minimum Value => '0',\n"
                ."    Maximum Value => '1000'\n"
                ."]\n",$rp.'');
    }
}
