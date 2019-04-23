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
    private function clrearVars() {
        foreach ($_GET as $k => $v){
            unset($_GET[$k]);
        }
        foreach ($_POST as $k => $v){
            unset($_POST[$k]);
        }
        foreach ($_REQUEST as $k => $v){
            unset($_REQUEST[$k]);
        }
        foreach ($_ENV as $k => $v){
            unset($_ENV[$k]);
        }
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $this->clrearVars();
        $api = new SampleAPI();
        $this->assertEquals('GET',$api->getRequestMethod());
        $this->assertNull($api->getAction());
        $this->assertEquals('1.0.0',$api->getVersion());
        $this->assertEquals('NO DESCRIPTION',$api->getDescription());
        $api->setDescription('Test API.');
        $this->assertEquals(1,count($api->getActions()));
        $this->assertEquals(4,count($api->getAuthActions()));
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
        $this->expectOutputString('{"message":"Action is not set.", "type":"error", "http-code":404}');
    }
    /**
     * @test
     */
    public function testActionAPIInfo00() {
        $this->clrearVars();
        $_GET['action'] = 'api-info';
        $_GET['pass'] = '123';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{'
                . '"api-version":"1.0.0", '
                . '"description":"NO DESCRIPTION", '
                . '"actions":['
                . '{'
                . '"name":"add-two-integers", '
                . '"since":"1.0.0", '
                . '"description":"Returns a JSON string that has the sum of two integers.", '
                . '"request-methods":["GET"], '
                . '"parameters":['
                . '{"name":"first-number", '
                . '"type":"integer", '
                . '"description":null, '
                . '"is-optional":false, '
                . '"default-value":null, '
                . '"min-val":-2147483648, '
                . '"max-val":2147483647}, '
                . '{"name":"second-number", '
                . '"type":"integer", '
                . '"description":null, '
                . '"is-optional":false, '
                . '"default-value":null, '
                . '"min-val":'.~PHP_INT_MAX.', '
                . '"max-val":'.PHP_INT_MAX.'}], '
                . '"responses":[]}], '
                . '"auth-actions":['
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
                . '"responses":[]}, '
                . '{"name":"sum-array", '
                . '"since":"1.0.0", '
                . '"description":"Returns a JSON string that has the sum of array of numbers.", '
                . '"request-methods":["POST", "GET"], '
                . '"parameters":[{"name":"numbers", '
                . '"type":"array", '
                . '"description":null, '
                . '"is-optional":false, '
                . '"default-value":null, '
                . '"min-val":null, "max-val":null}], "responses":[]}, '
                . '{"name":"get-user-profile", '
                . '"since":"1.0.0", '
                . '"description":"Returns a JSON string that has user profile info.", '
                . '"request-methods":["POST"], '
                . '"parameters":[{"name":"user-id", '
                . '"type":"integer", '
                . '"description":null, '
                . '"is-optional":false, '
                . '"default-value":null, '
                . '"min-val":'.~PHP_INT_MAX.', '
                . '"max-val":'.PHP_INT_MAX.'}], '
                . '"responses":[]}, '
                . '{"name":"do-nothing", '
                . '"since":"1.0.0", '
                . '"description":null, '
                . '"request-methods":["GET", "POST", "PUT", "DELETE"], '
                . '"parameters":[], "responses":[]}]}');
    }
    /**
     * @test
     */
    public function testActionAPIInfo01() {
        $this->clrearVars();
        $_GET['action'] = 'api-info';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"Not authorized.", "type":"error", "http-code":401}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers00() {
        $this->clrearVars();
        $_GET['first-number'] = '100';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The sum of 100 and 300 is 400.", "http-code":200}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers01() {
        $this->clrearVars();
        $_GET['first-number'] = '-100';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The sum of -100 and 300 is 200.", "http-code":200}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers02() {
        $this->clrearVars();
        $_GET['first-number'] = '1.8.89';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The sum of 1889 and 300 is 2189.", "http-code":200}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers03() {
        $this->clrearVars();
        $_GET['first-number'] = 'one';
        $_GET['second-number'] = 'two';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The following parameter(s) has invalid values: \'first-number\', \'second-number\'.", "type":"error", "http-code":404}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers04() {
        $this->clrearVars();
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The following required parameter(s) where missing from the request body: \'first-number\', \'second-number\'.", "type":"error", "http-code":404}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers05() {
        $this->clrearVars();
        $_GET['first-number'] = '-1.8.89';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The sum of -1889 and 300 is -1589.", "http-code":200}');
    }
    /**
     * @test
     */
    public function testSumTwoIntegers06() {
        $this->clrearVars();
        $_GET['first-number'] = '-1.8-8.89';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The following parameter(s) has invalid values: \'first-number\'.", "type":"error", "http-code":404}');
    }
    /**
     * @test
     */
    public function testSumArray00() {
        $this->clrearVars();
        $_GET['action'] = 'sum-array';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The following required parameter(s) where missing from the request body: \'numbers\'.", "type":"error", "http-code":404}');
    }
    /**
     * @test
     */
    public function testSumArray01() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_POST['action'] = 'sum-array';
        $_POST['numbers'] = '[m v b]';
        $api = new SampleAPI();
        $api->process();
        $this->expectOutputString('{"message":"The following parameter(s) has invalid values: \'numbers\'.", "type":"error", "http-code":404}');
    }
}
