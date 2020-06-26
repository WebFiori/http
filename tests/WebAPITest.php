<?php
namespace restEasy\tests;

use jsonx\JsonX;
use PHPUnit\Framework\TestCase;
use restEasy\WebService;
use restEasy\WebServices;
/**
 * Description of WebAPITest
 *
 * @author Eng.Ibrahim
 */
class WebAPITest extends TestCase {
    private $outputStreamName = __DIR__.DIRECTORY_SEPARATOR.'outputStream.txt';
    /**
     * @test
     */
    public function testActionAPIInfo00() {
        
        $this->clrearVars();
        $_GET['action'] = 'api-info';
        $_GET['pass'] = '123';
        $api = new SampleService();
        $this->assertTrue($api->setOutputStream($this->outputStreamName));
        $api->process();
        $this->assertEquals('{'
                .'"api-version":"1.0.1", '
                .'"description":"NO DESCRIPTION", '
                .'"actions":['
                .'{'
                .'"name":"add-two-integers", '
                .'"since":"1.0.0", '
                .'"description":"Returns a JSON string that has the sum of two integers.", '
                .'"request-methods":["GET"], '
                .'"parameters":['
                .'{"name":"first-number", '
                .'"type":"integer", '
                .'"description":null, '
                .'"is-optional":false, '
                .'"default-value":null, '
                .'"min-val":'.~PHP_INT_MAX.', '
                .'"max-val":'.PHP_INT_MAX.'}, '
                .'{"name":"second-number", '
                .'"type":"integer", '
                .'"description":null, '
                .'"is-optional":false, '
                .'"default-value":null, '
                .'"min-val":'.~PHP_INT_MAX.', '
                .'"max-val":'.PHP_INT_MAX.'}], '
                .'"responses":[]}], '
                .'"auth-actions":['
                .'{"name":"api-info", '
                .'"since":"1.0.0", '
                .'"description":"Returns a JSON string that contains all needed information about all end points in the given API.", '
                .'"request-methods":["GET"], '
                .'"parameters":['
                .'{"name":"version", '
                .'"type":"string", '
                .'"description":"Optional parameter. If set, the information that will be returned will be specific to the given version number.", '
                .'"is-optional":true, "default-value":null, '
                .'"min-val":null, "max-val":null}], '
                .'"responses":[]}, '
                .'{"name":"sum-array", '
                .'"since":"1.0.1", '
                .'"description":"Returns a JSON string that has the sum of array of numbers.", '
                .'"request-methods":["POST", "GET"], '
                .'"parameters":[{"name":"numbers", '
                .'"type":"array", '
                .'"description":null, '
                .'"is-optional":false, '
                .'"default-value":null, '
                .'"min-val":null, "max-val":null}], "responses":[]}, '
                .'{"name":"get-user-profile", '
                .'"since":"1.0.1", '
                .'"description":"Returns a JSON string that has user profile info.", '
                .'"request-methods":["POST"], '
                .'"parameters":[{"name":"user-id", '
                .'"type":"integer", '
                .'"description":null, '
                .'"is-optional":false, '
                .'"default-value":null, '
                .'"min-val":'.~PHP_INT_MAX.', '
                .'"max-val":'.PHP_INT_MAX.'}], '
                .'"responses":[]}, '
                .'{"name":"do-nothing", '
                .'"since":"1.0.1", '
                .'"description":null, '
                .'"request-methods":["GET", "POST", "PUT", "DELETE"], '
                .'"parameters":[], "responses":[]}]}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testActionAPIInfo01() {
        $this->clrearVars();
        $_GET['action'] = 'api-info';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Not authorized.", "type":"error", "http-code":401}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testActionAPIInfo02() {
        $this->clrearVars();
        $_GET['action'] = 'api-info';
        $_GET['pass'] = '123';
        $_GET['version'] = '1.0.1';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{'
                .'"api-version":"1.0.1", '
                .'"description":"NO DESCRIPTION", '
                .'"actions":[], '
                .'"auth-actions":['
                .'{"name":"sum-array", '
                .'"since":"1.0.1", '
                .'"description":"Returns a JSON string that has the sum of array of numbers.", '
                .'"request-methods":["POST", "GET"], '
                .'"parameters":[{"name":"numbers", '
                .'"type":"array", '
                .'"description":null, '
                .'"is-optional":false, '
                .'"default-value":null, '
                .'"min-val":null, "max-val":null}], "responses":[]}, '
                .'{"name":"get-user-profile", '
                .'"since":"1.0.1", '
                .'"description":"Returns a JSON string that has user profile info.", '
                .'"request-methods":["POST"], '
                .'"parameters":[{"name":"user-id", '
                .'"type":"integer", '
                .'"description":null, '
                .'"is-optional":false, '
                .'"default-value":null, '
                .'"min-val":'.~PHP_INT_MAX.', '
                .'"max-val":'.PHP_INT_MAX.'}], '
                .'"responses":[]}, '
                .'{"name":"do-nothing", '
                .'"since":"1.0.1", '
                .'"description":null, '
                .'"request-methods":["GET", "POST", "PUT", "DELETE"], '
                .'"parameters":[], "responses":[]}]}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testActionAPIInfo03() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'api-info';
        $_POST['pass'] = '123';
        $_POST['version'] = '1.0.1';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Method Not Allowed.", "type":"error", "http-code":405}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testAddAction00() {
        $api = new SampleService();
        $action00 = null;
        $this->assertFalse($api->addAction($action00));
        $action01 = 1;
        $this->assertFalse($api->addAction($action01));
        $action02 = 'string';
        $this->assertFalse($api->addAction($action02));
        $action03 = true;
        $this->assertFalse($api->addAction($action03));
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $api = new SampleService();
        $this->assertEquals('GET',$api->getRequestMethod());
        $this->assertNull($api->getAction());
        $this->assertEquals('1.0.1',$api->getVersion());
        $this->assertEquals('NO DESCRIPTION',$api->getDescription());
        $api->setDescription('Test API.');
        $this->assertEquals(1,count($api->getActions()));
        $this->assertEquals(4,count($api->getAuthActions()));
        $this->assertEquals('Test API.',$api->getDescription());
        $this->assertTrue($api->getActionByName('api-info') instanceof WebService);
        $this->assertNull($api->getActionByName('request-info'));
        $this->assertNull($api->getActionByName('api-info-2'));

        return $api;
    }
    /**
     * @test
     */
    public function testDoNothing00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=DELETE');
        $_GET['action'] = 'do-nothing';
        $_GET['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Action not implemented.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @depends testSumTwoIntegers05
     * @param WebServices $api
     */
    public function testGetNonFiltered00($api) {
        $nonFiltered = $api->getNonFiltered();
        $j = new JsonX();
        $j->add('non-filtered', $nonFiltered);
        $api->sendHeaders(['content-type' => 'application/json']);
        echo $j;
        $this->expectOutputString('{"non-filtered":[{"first-number":"-1.8.89"}, {"second-number":"300"}]}');
    }
    /**
     * @test
     */
    public function testGetUser00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['action'] = 'get-user-profile';
        $_GET['user-id'] = '-9';
        $_GET['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Method Not Allowed.", "type":"error", "http-code":405}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testGetUser01() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'get-user-profile';
        $_POST['user-id'] = '-9';
        $_POST['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Database Error.", "type":"error", "http-code":500, "more-info":""}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testGetUser02() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'get-user-profile';
        $_POST['user-id'] = '99';
        $_POST['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"user-name":"Ibrahim", "bio":"A software engineer who is ready to help anyone in need."}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testGetUser03() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_POST['action'] = 'get-user-profile';
        $_POST['user-id'] = '99';
        $_POST['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Content type not supported.", "type":"error", "http-code":404, "more-info":{"request-content-type":"application\/json"}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testGetUser04() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_POST['action'] = 'get-user-profile';
        $_POST['user-id'] = '99';
        $_POST['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Content type not supported.", "type":"error", "http-code":404, "more-info":{"request-content-type":null}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testNoActionInAPI() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=DELETE');
        $_GET['action'] = 'does-not-exist';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Action not supported.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     * @depends testConstructor00
     */
    public function testProcess00($api) {
        $this->clrearVars();
        $api->setOutputStream($this->outputStreamName);
        $api->process();

        $this->assertEquals('{"message":"Service name is not set.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSetVersion00() {
        $api = new SampleService();
        $this->assertTrue($api->setVersion('1065430.9000000009.10000087'));
        $this->assertEquals('1065430.9000000009.10000087',$api->getVersion());
        $this->assertFalse($api->setVersion('6Y.00o0.76T'));
        $this->assertEquals('1065430.9000000009.10000087',$api->getVersion());
        $this->assertFalse($api->setVersion('1.0.9.0.8'));
    }
    /**
     * @test
     */
    public function testSumArray00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['action'] = 'sum-array';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following required parameter(s) where missing from the request body: \'numbers\'.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumArray01() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'sum-array';
        $_POST['numbers'] = '[m v b]';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'numbers\'.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumArray02() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'sum-array';
        $_POST['numbers'] = '[1,2,"as",1.9,\'hello\',10]';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Not authorized.", "type":"error", "http-code":401}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumArray03() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'sum-array';
        $_POST['numbers'] = '[1,2,"as",1.9,\'hello\',10]';
        $_POST['pass'] = '123';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"sum":14.9}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '100';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of 100 and 300 is 400.", "http-code":200}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers01() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '-100';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of -100 and 300 is 200.", "http-code":200}', $api->readOutputStream());
    }

    /**
     * @test
     */
    public function testSumTwoIntegers02() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '1.8.89';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of 1889 and 300 is 2189.", "http-code":200}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers03() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = 'one';
        $_GET['second-number'] = 'two';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'first-number\', \'second-number\'.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers04() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following required parameter(s) where missing from the request body: \'first-number\', \'second-number\'.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers05() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '-1.8.89';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of -1889 and 300 is -1589.", "http-code":200}', $api->readOutputStream());

        return $api;
    }
    /**
     * @test
     */
    public function testSumTwoIntegers06() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '-1.8-8.89';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'first-number\'.", "type":"error", "http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers07() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['first-number'] = '100';
        $_POST['second-number'] = '300';
        $_POST['action'] = 'add-two-integers';
        $api = new SampleService();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Method Not Allowed.", "type":"error", "http-code":405}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSetOutputStream00() {
        $api = new SampleService();
        $this->assertFalse($api->setOutputStream(null));
        $this->assertFalse($api->setOutputStream(''));
        $this->assertFalse($api->setOutputStream('null'));
        $this->assertFalse($api->setOutputStream(' X:\server\no'));
        $this->assertNull($api->getOutputStream());
        $this->assertNull($api->getOutputStreamPath());
    }
    /**
     * @test
     */
    public function testSetOutputStream01() {
        $api = new SampleService();
        $stream = fopen(__DIR__.DIRECTORY_SEPARATOR.'hello.txt', 'w');
        $this->assertTrue($api->setOutputStream($stream));
        $this->assertNotNull($api->getOutputStream());
        $this->assertNull($api->getOutputStreamPath());
    }
    /**
     * @test
     */
    public function testSetOutputStream02() {
        $api = new SampleService();
        $this->assertTrue($api->setOutputStream(__DIR__.DIRECTORY_SEPARATOR.'hello2.txt', true));
        $this->assertNotNull($api->getOutputStream());
        $this->assertNotNull($api->getOutputStreamPath());
        $this->assertEquals(__DIR__.DIRECTORY_SEPARATOR.'hello2.txt', $api->getOutputStreamPath());
    }
    /**
     * @test
     */
    public function testSetOutputStream03() {
        $api = new SampleService();
        $this->assertTrue($api->setOutputStream(__DIR__.DIRECTORY_SEPARATOR.'outputStream.txt', true));
        $this->assertNotNull($api->getOutputStream());
        $this->assertNotNull($api->getOutputStreamPath());
        $this->assertEquals(__DIR__.DIRECTORY_SEPARATOR.'outputStream.txt', $api->getOutputStreamPath());
    }
    private function clrearVars() {
        foreach ($_GET as $k => $v) {
            unset($_GET[$k]);
        }

        foreach ($_POST as $k => $v) {
            unset($_POST[$k]);
        }

        foreach ($_REQUEST as $k => $v) {
            unset($_REQUEST[$k]);
        }

        foreach ($_ENV as $k => $v) {
            unset($_ENV[$k]);
        }
        unset($_SERVER['CONTENT_TYPE']);
    }
}
