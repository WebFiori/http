<?php
namespace WebFiori\Tests\Http;

use Exception;
use PHPUnit\Framework\TestCase;
use WebFiori\Http\APIFilter;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestParameter;
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
        $param = RequestParameter::create([]);
        $this->assertNull($param);
    }
    /**
     * @test
     */
    public function testCreateParameter01() {
        $param = RequestParameter::create([
            'name'=>'invalid name'
        ]);
        $this->assertNotNull($param);
        $this->assertEquals('a-parameter', $param->getName());
    }
    /**
     * @test
     */
    public function testCreateParameter02() {
        $param = RequestParameter::create([
            ParamOption::NAME =>'hello',
            ParamOption::TYPE => 'integer',
            'min' => 33,
            'max' => 100,
            'custom-filter' => function ($original, $basicFilterResult, $param) {
                if ($basicFilterResult != APIFilter::INVALID) {
                    return $basicFilterResult * 100;
                }
            }
        ]);
        $this->assertNotNull($param);
        $this->assertEquals('hello', $param->getName());
        $this->assertEquals('integer', $param->getType());
        $this->assertEquals(33, $param->getMinValue());
        $this->assertEquals(100, $param->getMaxValue());
        $this->assertTrue(is_callable($param->getCustomFilterFunction()));
    }
    /**
     * @test
     */
    public function testCreateParameter03() {
        $param = RequestParameter::create([
            'name'=>'ok',
            ParamOption::TYPE => ParamType::STRING,
            ParamOption::DEFAULT => 'Ibrahim',
            ParamOption::EMPTY => true,
            ParamOption::DESCRIPTION => 'Super param.'
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
        $param = RequestParameter::create([
            'name'=>'ok',
            ParamOption::TYPE => 'int',
            ParamOption::DEFAULT => 44,
            ParamOption::DESCRIPTION => 'Super param.'
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
        $this->assertNull($requestParam->getMaxValue());
        $this->assertNull($requestParam->getMinValue());
        $this->assertNull($requestParam->getMaxLength());
        $this->assertNull($requestParam->getMinLength());
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
        $this->assertNull($requestParam->getMaxValue());
        $this->assertNull($requestParam->getMinValue());
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
        $this->assertNull($requestParam->getMaxValue());
        $this->assertNull($requestParam->getMinValue());
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
        $this->assertEquals(PHP_INT_MAX, $requestParam->getMaxValue());
        $this->assertEquals(~PHP_INT_MAX,$requestParam->getMinValue());
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
        $this->assertEquals(PHP_INT_MAX, $requestParam->getMaxValue());
        $this->assertEquals(~PHP_INT_MAX,$requestParam->getMinValue());
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
        $this->assertNull($requestParam->getMaxValue());
        $this->assertNull($requestParam->getMinValue());
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

        $this->assertEquals(1e50, $requestParam->getMaxValue());
        $this->assertEquals(-1e50,$requestParam->getMinValue());
        $this->assertNull($requestParam->getDefault());
        $this->assertNull($requestParam->getDescription());
        $this->assertNull($requestParam->getCustomFilterFunction());
        $this->assertEquals('double',$requestParam->getType());
        $this->assertTrue($requestParam->setType('int'));
        $this->assertEquals('integer',$requestParam->getType());
    }
    /**
     * @test
     */
    public function testSetCustomFilter01() {
        $rp = new RequestParameter('hello');
        $rp->setCustomFilterFunction(function() {
        },false);
        $this->assertTrue(is_callable($rp->getCustomFilterFunction()));
        $this->assertFalse($rp->isBasicFilter());
    }
    /**
     * @test
     */
    public function testSetCustomFilter02() {
        $rp = new RequestParameter('hello');
        $rp->setCustomFilterFunction(function() {
        },true);
        $this->assertTrue(is_callable($rp->getCustomFilterFunction()));
        $this->assertTrue($rp->isBasicFilter());
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setDefault(new Exception()));
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
        $this->assertFalse($rp->setMaxValue(5));
        $this->assertNull($rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax01() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMaxValue(5));
        $this->assertEquals(5,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax02() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMaxValue('5'));
        $this->assertEquals(5,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax03() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMaxValue(66.90));
        $this->assertEquals(66,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax04() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMaxValue(5.6));
        $this->assertEquals(5.6,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax05() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMaxValue('5'));

        
        $this->assertSame(5.0,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax06() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMaxValue(66));
        $this->assertEquals(66,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMax07() {
        $rp = new RequestParameter('val','integer');
        $rp->setMinValue(0);
        $this->assertFalse($rp->setMaxValue(-66));
        $this->assertEquals(PHP_INT_MAX,$rp->getMaxValue());
        $this->assertFalse($rp->setMaxValue(0));
        $this->assertTrue($rp->setMaxValue(1));
        $this->assertEquals(1,$rp->getMaxValue());
    }
    /**
     * @test
     */
    public function testSetMin00() {
        $rp = new RequestParameter('val');
        $this->assertFalse($rp->setMinValue(5));
        $this->assertNull($rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin01() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMinValue(5));
        $this->assertEquals(5,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin02() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMinValue('5'));
        $this->assertSame(5,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin03() {
        $rp = new RequestParameter('val','integer');
        $this->assertTrue($rp->setMinValue(66.90));
        $this->assertSame(66,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin04() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMinValue(5.6));
        $this->assertEquals(5.6,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin05() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMinValue('5'));
        $this->assertSame(5.0,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin06() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertTrue($rp->setMinValue(66));
        $this->assertEquals(66,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMin07() {
        $rp = new RequestParameter('val','integer');
        $rp->setMaxValue(-100);
        $this->assertFalse($rp->setMinValue(66));
        $this->assertEquals(~PHP_INT_MAX,$rp->getMinValue());
        $this->assertFalse($rp->setMinValue(-100));
        $this->assertTrue($rp->setMinValue(-101));
        $this->assertEquals(-101,$rp->getMinValue());
    }
    /**
     * @test
     */
    public function testSetMaxLength00() {
        $rp = new RequestParameter('val');
        $this->assertFalse($rp->setMaxLength(0));
        $this->assertNull($rp->getMaxLength());
    }
    /**
     * @test
     */
    public function testSetMaxLength01() {
        $rp = new RequestParameter('val');
        $this->assertTrue($rp->setMaxLength(5));
        $this->assertEquals(5,$rp->getMaxLength());
    }
    /**
     * @test
     */
    public function testSetMaxLength02() {
        $rp = new RequestParameter('val','url');
        $this->assertTrue($rp->setMaxLength('5'));
        $this->assertEquals(5,$rp->getMaxLength());
    }
    /**
     * @test
     */
    public function testSetMaxLength03() {
        $rp = new RequestParameter('val','email');
        $this->assertTrue($rp->setMaxLength(66));
        $this->assertEquals(66,$rp->getMaxLength());
    }
    /**
     * @test
     */
    public function testSetMaxlength04() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertFalse($rp->setMaxLength(5));
        $this->assertNull($rp->getMaxLength());
    }
    /**
     * @test
     */
    public function testSetMaxLength05() {
        $rp = new RequestParameter('val','email');
        $rp->setMinLength(10);
        $this->assertFalse($rp->setMaxLength(9));
        $this->assertNull($rp->getMaxLength());
        $this->assertTrue($rp->setMaxLength(10));
        $this->assertEquals(10, $rp->getMaxLength());
    }
    /**
     * @test
     */
    public function testSetMinLength00() {
        $rp = new RequestParameter('val');
        $this->assertFalse($rp->setMinLength(0));
        $this->assertNull($rp->getMinLength());
    }
    /**
     * @test
     */
    public function testSetMinLength01() {
        $rp = new RequestParameter('val');
        $this->assertTrue($rp->setMinLength(5));
        $this->assertEquals(5,$rp->getMinLength());
    }
    /**
     * @test
     */
    public function testSetMinLength02() {
        $rp = new RequestParameter('val','url');
        $this->assertTrue($rp->setMinLength('5'));
        $this->assertEquals(5,$rp->getMinLength());
    }
    /**
     * @test
     */
    public function testSetMinLength03() {
        $rp = new RequestParameter('val','email');
        $this->assertTrue($rp->setMinLength(66));
        $this->assertEquals(66,$rp->getMinLength());
    }
    /**
     * @test
     */
    public function testSetMinlength04() {
        $rp = new RequestParameter('val','float');
        $this->assertEquals('double',$rp->getType());
        $this->assertFalse($rp->setMinLength(5));
        $this->assertNull($rp->getMinLength());
    }
    /**
     * @test
     */
    public function testSetMinLength05() {
        $rp = new RequestParameter('val','email');
        $rp->setMaxLength(10);
        $this->assertFalse($rp->setMinLength(11));
        $this->assertNull($rp->getMinLength());
        $this->assertTrue($rp->setMinLength(10));
        $this->assertEquals(10, $rp->getMinLength());
    }
    /**
     * @test
     * @depends testConstructor00
     * @param RequestParameter $reqParam
     */
    public function testToJson00($reqParam) {
        $reqParam->setDescription('Test Parameter.');
        $this->assertEquals('{"name":"a-parameter","in":"query","required":true,"description":"Test Parameter.","schema":{"type":"string"}}',$reqParam->toJSON().'');
    }
    /**
     * @test
     * @depends testConstructor03
     * @param RequestParameter $reqParam
     */
    public function testToJson01($reqParam) {
        $reqParam->setDescription('Test Parameter.');
        $this->assertEquals('{"name":"valid","in":"query","required":false,"description":"Test Parameter.","schema":{"type":"integer","minimum":'.~PHP_INT_MAX.',"maximum":'.PHP_INT_MAX.'}}',$reqParam->toJSON().'');
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
                ."    Maximum Value => 'null',\n"
                ."    Minimum Length => 'null',\n"
                ."    Maximum Length => 'null'\n"
                ."]\n",$rp.'');
    }
    /**
     * 
     * @test
     */
    public function testToString01() {
        $rp = new RequestParameter('user-id','integer');
        $rp->setMinValue(0);
        $rp->setMaxValue(1000);
        $rp->setDescription(' The ID of the user. ');
        $this->assertEquals("RequestParameter[\n"
                ."    Name => 'user-id',\n"
                ."    Type => 'integer',\n"
                ."    Description => 'The ID of the user.',\n"
                ."    Is Optional => 'false',\n"
                ."    Default => 'null',\n"
                ."    Minimum Value => '0',\n"
                ."    Maximum Value => '1000',\n"
                ."    Minimum Length => 'null',\n"
                ."    Maximum Length => 'null'\n"
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
                ."    Maximum Value => '1000',\n"
                ."    Minimum Length => 'null',\n"
                ."    Maximum Length => 'null'\n"
                ."]\n",$rp.'');

    }
    /**
     * @test
     */
    public function testRequestMethod00() {
        $rp = new RequestParameter('user-id','integer');
        $this->assertEquals([], $rp->getMethods());
        $rp->addMethod('get');
        $this->assertEquals(['GET'], $rp->getMethods());
        $rp->addMethods(['geT', 'PoSt ']);
        $this->assertEquals(['GET', 'POST'], $rp->getMethods());
    }
    
    public function testReservedParameterName() {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('reserved');
        new RequestParameter('service', 'string');
    }
    
    public function testToJSONWithMethods() {
        $rp = new RequestParameter('user-id', 'integer');
        $rp->addMethod('POST');
        $json = $rp->toJSON();
        $this->assertEquals('body', $json->get('in'));
    }
    
    public function testSetCustomFilter() {
        $rp = new RequestParameter('custom', 'string');
        $rp->setCustomFilterFunction(function($val) {
            return strtoupper($val);
        });
        $this->assertNotNull($rp->getCustomFilterFunction());
    }
    
    public function testSetDescription() {
        $rp = new RequestParameter('test', 'string');
        $rp->setDescription('Test parameter');
        $this->assertEquals('Test parameter', $rp->getDescription());
    }
    
    public function testIsEmptyStringAllowed() {
        $rp = new RequestParameter('test', 'string');
        $rp->setIsEmptyStringAllowed(true);
        $this->assertTrue($rp->isEmptyStringAllowed());
    }
}

