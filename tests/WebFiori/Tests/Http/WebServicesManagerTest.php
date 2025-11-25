<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\AbstractWebService;
use WebFiori\Http\APITestCase;
use WebFiori\Http\RequestV2;
use WebFiori\Http\ResponseMessage;
use WebFiori\Http\WebServicesManager;
use WebFiori\Json\Json;
use WebFiori\Tests\Http\TestServices\NoAuthService;
use WebFiori\Tests\Http\TestServices\NotImplService;
use WebFiori\Tests\Http\TestServices\SampleServicesManager;
/**
 * Description of WebAPITest
 *
 * @author Eng.Ibrahim
 */
class WebServicesManagerTest extends APITestCase {
    private $outputStreamName = __DIR__.DIRECTORY_SEPARATOR.'outputStream.txt';
    public function test00() {
        $manager = new WebServicesManager();
        $manager->addService(new NoAuthService());
        $this->assertEquals('{'.self::NL
                . '    "message":"You are authorized.",'.self::NL
                . '    "http-code":200'.self::NL
                . '}', $this->getRequest($manager, 'ok-service'));
        return $manager;
    }
    /**
     * @test
     */

    public function test01() {
        $manager = new WebServicesManager();
        $manager->addService(new NotImplService());
        $this->assertEquals('{'.self::NL
                . '    "message":"Service not implemented.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":404'.self::NL
                . '}', $this->postRequest($manager, 'not-implemented'));
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
        $this->assertEquals('GET', RequestV2::createFromGlobals()->getRequestMethod());
        $this->assertNull($api->getCalledServiceName());
        $this->assertEquals('1.0.1',$api->getVersion());
        $this->assertEquals('NO DESCRIPTION',$api->getDescription());
        $api->setDescription('Test API.');
        $this->assertEquals(5,count($api->getServices()));
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
        $api = new SampleServicesManager();
        $this->assertEquals('{'.self::NL
                . '    "message":"Service not supported.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":404'.self::NL
                . '}', $this->getRequest($api, 'do-nothen'));
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
        $api = new SampleServicesManager();
        $this->assertEquals('{'.self::NL
                . '    "message":"Method Not Allowed.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":405'.self::NL
                . '}', $this->getRequest($api, 'get-user-profile', [
            'user-id' => -9,
            'pass' => '123'
        ]));
    }
    /**
     * @test
     */
    public function testGetUser01() {
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $api = new SampleServicesManager();
        $this->assertEquals('{'.self::NL
                . '    "message":"Database Error.",'.self::NL
                . '    "type":"error",'.self::NL
                . '    "http-code":500'.self::NL
                . '}', $this->postRequest($api, 'get-user-profile', [
            'user-id' => -9,
            'pass' => '123'
        ]));

    }
    /**
     * @test
     */
    public function testGetUser02() {
        $api = new SampleServicesManager();

        $this->assertEquals('{'.self::NL
                . '    "user-name":"Ibrahim",'.self::NL
                . '    "bio":"A software engineer who is ready to help anyone in need."'.self::NL
                . '}', $this->postRequest($api, 'get-user-profile', [
            'user-id' => '99',
            'pass' => '123'
        ]));

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
        $this->assertEquals('{"message":"Content type not supported.","type":"error","http-code":415,"more-info":{"request-content-type":"application\/xml"}}', $api->readOutputStream());
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
        $this->assertEquals('{"message":"Content type not supported.","type":"error","http-code":415,"more-info":{"request-content-type":"NOT_SET"}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testCreateUser00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['service'] = 'create-user-profile';
        $_POST['id'] = '99';
        $_POST['name'] = 'Ibrahim';
        $_POST['username'] = 'WarriorX';
        $_POST['pass'] = '123';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"user":{"Id":3,"FullName":"Ibrahim","Username":"WarriorX"}}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testCreateUser01() {
        //Start Setup
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $jsonTestFile = __DIR__.DIRECTORY_SEPARATOR.'json.json';
        self::setTestJson($jsonTestFile,'{"service":"create-user-profile","pass":"123","name":"Me", "username":"Cpool", "id":54}');
        $manager = new SampleServicesManager();
        $manager->setInputStream(fopen($jsonTestFile, 'r'));
        $manager->setOutputStream(fopen($this->outputStreamName,'w'));
        //End Setup
        
        $manager->process();
        $this->assertEquals('{"user":{"Id":3,"FullName":"Me","Username":"Cpool"}}', $manager->readOutputStream());
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
        $this->assertEquals('{"message":"Not Authorized.","type":"error","http-code":401}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testSumArray04() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST['action'] = 'sum-array';
        $_POST['numbers'] = '[1,2,"as",1.9,\'hello\',10]';
        $_POST['pass'] = '1234';
        $api = new SampleServicesManager();
        ResponseMessage::set('401', 'Your password is inncorrect.');
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"Your password is inncorrect.","type":"error","http-code":401}', $api->readOutputStream());
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
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'first-number\'.","type":"error","http-code":404,"more-info":{"invalid":["first-number"]}}', $api->readOutputStream());
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
        $this->assertEquals('{"message":"The following parameter(s) has invalid values: \'first-number\'.","type":"error","http-code":404,"more-info":{"invalid":["first-number"]}}', $api->readOutputStream());

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
    public function testMulTwoIntegers00() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '100';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'mul-two-integers';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        $api->process();
        $this->assertEquals('{"message":"The multiplication of 100 and 300 is 30000.","http-code":200}', $api->readOutputStream());
    }
    /**
     * @test
     */
    public function testMulTwoIntegers01() {
        $this->clrearVars();
        putenv('REQUEST_METHOD=GET');
        $_GET['first-number'] = '-100';
        $_GET['second-number'] = '300';
        $_GET['action'] = 'mul-two-integers';
        $api = new SampleServicesManager();
        $api->setOutputStream($this->outputStreamName);
        ResponseMessage::set(401, 'First number must be positive!');
        $api->process();
        $this->assertEquals('{"message":"First number must be positive!","type":"error","http-code":401}', $api->readOutputStream());
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
