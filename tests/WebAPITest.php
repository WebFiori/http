<?php
namespace restEasy\tests;
use PHPUnit\Framework\TestCase;
use restEasy\RequestParameter;
use restEasy\tests\SampleAPI;
use restEasy\APIAction;
/**
 * Description of WebAPITest
 *
 * @author Eng.Ibrahim
 */
class WebAPITest extends TestCase{
    /**
     * @test
     */
    public function testConstructor00() {
        $api = new SampleAPI();
        $this->assertEquals('GET',$api->getRequestMethod());
        $this->assertNull($api->getAction());
        $this->assertEquals('1.0.0',$api->getVersion());
        $this->assertEquals('NO DESCRIPTION',$api->getDescription());
        $api->setDescription('Test API.');
        $this->assertEquals(0,count($api->getActions()));
        $this->assertEquals(1,count($api->getAuthActions()));
        $this->assertEquals('Test API.',$api->getDescription());
        $this->assertTrue($api->getActionByName('api-info') instanceof APIAction);
        $this->assertNull($api->getActionByName('request-info'));
        $this->assertNull($api->getActionByName('api-info-2'));
        return $api;
    }
    /**
     * @test
     * @depends testConstructor00
     */
    public function testProcess00($api) {
        $api->process();
        $this->expectOutputString('{"message":"Action is not set.","type":"error"}');
    }
    /**
     * @test
     */
    public function testActionAPIInfo00() {
        $_GET['action'] = 'api-info';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{'
                . '"api-version":"1.0.0", '
                . '"method":"GET", "description":"NO DESCRIPTION", '
                . '"actions":[], "auth-actions":['
                . '{"name":"api-info", '
                . '"since":"1.0.0", '
                . '"description":"Returns a JSON string that contains all needed information about all end points in the given API.", '
                . '"request-methods":["GET"], '
                . '"parameters":['
                . '{"name":"version", '
                . '"type":"string", '
                . '"description":"Optional parameter. If set, the information that will be returned will be specific to the given version number.", '
                . '"is-optional":true, "default-value":null, '
                . '"min-val":null, "max-val":null}], '
                . '"responses":[]}'
                . ']}');
    }
}
