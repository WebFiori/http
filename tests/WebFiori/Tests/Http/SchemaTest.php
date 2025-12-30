<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\OpenAPI\Schema;
use WebFiori\Http\RequestParameter;
use WebFiori\Http\ParamType;

class SchemaTest extends TestCase {
    
    public function testSchemaBasic() {
        $schema = new Schema('string');
        $json = $schema->toJSON();
        $this->assertEquals('string', $json->get('type'));
    }
    
    public function testSchemaInteger() {
        $schema = new Schema('integer');
        $json = $schema->toJSON();
        $this->assertEquals('integer', $json->get('type'));
    }
    
    public function testFromRequestParameterEmail() {
        $param = new RequestParameter('email', ParamType::EMAIL);
        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();
        $this->assertEquals('email', $json->get('format'));
    }
    
    public function testFromRequestParameterUrl() {
        $param = new RequestParameter('website', ParamType::URL);
        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();
        $this->assertEquals('uri', $json->get('format'));
    }
    
    public function testFromRequestParameterWithMinMax() {
        $param = new RequestParameter('age', ParamType::INT);
        $param->setMinValue(0);
        $param->setMaxValue(120);
        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();
        $this->assertEquals(0, $json->get('minimum'));
        $this->assertEquals(120, $json->get('maximum'));
    }
    
    public function testFromRequestParameterWithLength() {
        $param = new RequestParameter('name', ParamType::STRING);
        $param->setMinLength(2);
        $param->setMaxLength(50);
        $schema = Schema::fromRequestParameter($param);
        $json = $schema->toJSON();
        $this->assertEquals(2, $json->get('minLength'));
        $this->assertEquals(50, $json->get('maxLength'));
    }
}
