<?php
namespace webfiori\tests\http;

use webfiori\json\Json;
use PHPUnit\Framework\TestCase;
use webfiori\http\AbstractWebService;
use webfiori\http\WebServicesManager;
use webfiori\tests\http\testServices\NotImplService;
use webfiori\tests\http\testServices\SampleServicesManager;
use webfiori\tests\http\testServices\NoAuthService;
/**
 * Description of WebAPITest
 *
 * @author Eng.Ibrahim
 */
class WebServicesManagerTest extends TestCase {
    private $outputStreamName = __DIR__.DIRECTORY_SEPARATOR.'outputStream.txt';
    /**
     * @test
     */

    public function test00() {
        $this->clrearVars();
        $manager = new WebServicesManager();
        $manager->addService(new NoAuthService());
        $_GET['service'] = 'ok-service';
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        $manager->process();
        $this->assertEquals('{"message":"You are authorized.","http-code":200}', $manager->readOutputStream());
        return $manager;
    }
    /**
     * @test
     */

    public function test01() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $manager = new WebServicesManager();
        $manager->addService(new NotImplService());
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
        $_POST['service'] = 'not-implemented';
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        $manager->process();
        $this->assertEquals('{"message":"Service not implemented.","type":"error","http-code":404}', $manager->readOutputStream());
        return $manager;
    }
    /**
     * @test
     */
    public function testJson00() {
        //Start Setup
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setTestJson($jsonTestFile, '{}');
        $manager = new WebServicesManager();
        $manager->setInputStream($jsonTestFile);
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        //End Setup
        
        
        $manager->process();
        $this->assertEquals('{"message":"Service name is not set.","type":"error","http-code":404}', $manager->readOutputStream());
    }
    /**
     * @test
     */
    public function testJson01() {
        //Start Setup
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setTestJson($jsonTestFile, '{"service":"not-exist"}');
        $manager = new WebServicesManager();
        $manager->setInputStream($jsonTestFile);
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        //End Setup
        
        
        $manager->process();
        $this->assertEquals('{"message":"Service not supported.","type":"error","http-code":404}', $manager->readOutputStream());
    }
    /**
     * @test
     */
    public function testJson02() {
        //Start Setup
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setTestJson($jsonTestFile, '{"service":"not-implemented"}');
        $manager = new WebServicesManager();
        $manager->addService(new NotImplService());
        $manager->setInputStream(fopen($jsonTestFile, 'r'));
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        //End Setup
        
        
        $manager->process();
        $this->assertEquals('{"message":"Service not implemented.","type":"error","http-code":404}', $manager->readOutputStream());
    }
    /**
     * @test
     */
    public function testJson03() {
        //Start Setup
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setTestJson($jsonTestFile, '{"service":"sum-array"}');
        $manager = new SampleServicesManager();
        $manager->setInputStream(fopen($jsonTestFile, 'r'));
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        //End Setup
        
        
        $manager->process();
        $this->assertEquals('{"message":"The following required parameter(s) where missing from the request body: \'pass\', \'numbers\'.","type":"error","http-code":404,"more-info":{"missing":["pass","numbers"]}}', $manager->readOutputStream());
    }
    /**
     * @test
     */
    public function testJson04() {
        //Start Setup
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setTestJson($jsonTestFile,'{"service":"sum-array","pass":"123","numbers":[1,5,4,1.5]}');
        $manager = new SampleServicesManager();
        $manager->setInputStream(fopen($jsonTestFile, 'r'));
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        //End Setup
        
        
        $manager->process();
        $this->assertEquals('{"sum":11.5}', $manager->readOutputStream());
    }
    /**
     * 
     * @param WebServicesManager $manager
     * @depends test00
     */
    public function testRemoveService00(WebServicesManager $manager) {
        $this->assertNull($manager->removeService('xyz'));
        $service = $manager->removeService('ok-service');
        $this->assertTrue($service instanceof AbstractWebService);
        $this->assertEquals(0, count($manager->getServices()));
        $this->assertNull($service->getManager());
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $api = new SampleServicesManager();
        $this->assertEquals('GET', \webfiori\http\Request::getMethod());
        $this->assertNull($api->getCalledServiceName());
        $this->assertEquals('1.0.1',$api->getVersion());
        $this->assertEquals('NO DESCRIPTION',$api->getDescription());
        $api->setDescription('Test API.');
        $this->assertEquals(3,count($api->getServices()));
        $this->assertEquals('Test API.',$api->getDescription());
        $this->assertTrue($api->getServiceByName('sum-array') instanceof AbstractWebService);
        $this->assertNull($api->getServiceByName('request-info'));
        $this->assertNull($api->getServiceByName('api-info-2'));

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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Service not supported.","type":"error","http-code":404}', $api->readOutputStream());
    }
    /**
     * @depends testSumTwoIntegers05
     * @param WebServicesManager $api
     */
    public function testGetNonFiltered00($api) {
        $nonFiltered = $api->getNonFiltered();
        $j = new Json();
        $j->add('non-filtered', $nonFiltered, true);
        $api->sendHeaders(['content-type' => 'application/json']);
        echo $j;
        $this->expectOutputString('{"non-filtered":{"pass":"123","first-number":"-1.8.89","second-number":"300"}}');
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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Method Not Allowed.","type":"error","http-code":405}', $api->readOutputStream());
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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Database Error.","type":"error","http-code":500,"more-info":""}', $api->readOutputStream());
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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"user-name":"Ibrahim","bio":"A software engineer who is ready to help anyone in need."}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testGetUser03() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        $_POST['action'] = 'get-user-profile';
        $_POST['user-id'] = '99';
        $_POST['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Content type not supported.","type":"error","http-code":404,"more-info":{"request-content-type":"application\/xml"}}', $api->readOutputStream());
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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Content type not supported.","type":"error","http-code":404,"more-info":{"request-content-type":"NOT_SET"}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testNoActionInAPI() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=DELETE');
        $_GET['action'] = 'does-not-exist';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Service not supported.","type":"error","http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     * @depends testConstructor00
     */
    public function testProcess00($api) {
        $this->clrearVars();
        $api->setOutputStream($this->outputStreamName);
        $api->process();

        $this->assertEquals('{"message":"Service name is not set.","type":"error","http-code":404}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSetVersion00() {
        $api = new SampleServicesManager();
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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following required parameter(s) where missing from the request body: \'pass\', \'numbers\'.","type":"error","http-code":404,"more-info":{"missing":["pass","numbers"]}}', $api->readOutputStream());
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
        $_POST['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'numbers\'.","type":"error","http-code":404,"more-info":{"invalid":["numbers"]}}', $api->readOutputStream());
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
        $_POST['pass'] = '1234';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Not authorized.","type":"error","http-code":401}', $api->readOutputStream());
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
        $api = new SampleServicesManager();
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
        $_GET['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of 100 and 300 is 400.","http-code":200}', $api->readOutputStream());
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
        $_GET['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of -100 and 300 is 200.","http-code":200}', $api->readOutputStream());
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
        $_GET['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of 1889 and 300 is 2189.","http-code":200}', $api->readOutputStream());
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
        $_GET['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'first-number\', \'second-number\'.","type":"error","http-code":404,"more-info":{"invalid":["first-number","second-number"]}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers04() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['service'] = 'add-two-integers';
        $_GET['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following required parameter(s) where missing from the request body: \'first-number\', \'second-number\'.","type":"error","http-code":404,"more-info":{"missing":["first-number","second-number"]}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumTwoIntegers05() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '-1.8.89';
        $_GET['second-number'] = '300';
        $_GET['pass'] = '123';
        $_GET['service-name'] = 'add-two-integers';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The sum of -1889 and 300 is -1589.","http-code":200}', $api->readOutputStream());

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
        $_GET['pass'] = '123';
        $_GET['action'] = 'add-two-integers';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'first-number\'.","type":"error","http-code":404,"more-info":{"invalid":["first-number"]}}', $api->readOutputStream());
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
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Method Not Allowed.","type":"error","http-code":405}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSetOutputStream00() {
        $api = new SampleServicesManager();
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
        $api = new SampleServicesManager();
        $stream = fopen(__DIR__.DIRECTORY_SEPARATOR.'hello.txt', 'w');
        $this->assertTrue($api->setOutputStream($stream));
        $this->assertNotNull($api->getOutputStream());
        $this->assertNotNull($api->getOutputStreamPath());
        $this->assertEquals(__DIR__.DIRECTORY_SEPARATOR.'hello.txt',$api->getOutputStreamPath());
    }
    /**
     * @test
     */
    public function testSetOutputStream02() {
        $api = new SampleServicesManager();
        $this->assertTrue($api->setOutputStream(__DIR__.DIRECTORY_SEPARATOR.'hello2.txt', true));
        $this->assertNotNull($api->getOutputStream());
        $this->assertNotNull($api->getOutputStreamPath());
        $this->assertEquals(__DIR__.DIRECTORY_SEPARATOR.'hello2.txt', $api->getOutputStreamPath());
    }
    /**
     * @test
     */
    public function testSetOutputStream03() {
        $api = new SampleServicesManager();
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
        putenv('REQUEST_METHOD');
    }
    public static function setTestJson($fName, $jsonData) {
        $stream = fopen($fName, 'w+');
        fwrite($stream, $jsonData);
        fclose($stream);
    }
}
