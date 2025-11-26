<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\Request;

/**
 * Description of RequestTest
 *
 * @author Ibrahim
 */
class RequestTest extends TestCase {
    private $request;
    /**
     * 
     */
    public function testGetParam00() {
        $this->request = Request::createFromGlobals();
        $this->assertNull($this->request->getParam('not-exist'));
    }
    /**
     * @test
     */
    public function testGetParam01() {
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
        $_POST['ok'] = 'I Ok';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('I Ok', $this->request->getParam('ok'));
    }
    /**
     * @test
     */
    public function testGetParam02() {
        putenv('REQUEST_METHOD=GET');
        $_GET['ok'] = 'I not Ok';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('I not Ok', $this->request->getParam('ok'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetParam03() {
        putenv('REQUEST_METHOD=POST');
        $this->request = Request::createFromGlobals();
        $this->assertNull($this->request->getParam('not-exist'));
    }
    /**
     * @test
     */
    public function testGetParam04() {
        putenv('REQUEST_METHOD=GET');
        $this->request = Request::createFromGlobals();
        $this->assertNull($this->request->getParam('not-exist'));
    }
    /**
     * @test
     */
    public function testGetParam05() {
        putenv('REQUEST_METHOD=GET');
        $_GET['hello'] = 'This%20is%20an%20encoded%20string.';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('This is an encoded string.', $this->request->getParam('hello'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetParam06() {
        putenv('REQUEST_METHOD=GET');
        $_GET['hello'] = 'This+is+an+encoded%20string.';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('This is an encoded string.', $this->request->getParam('hello'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetParams06() {
        putenv('REQUEST_METHOD=GET');
        $_GET['arabic'] = '%D9%86%D8%B5%20%D8%B9%D8%B1%D8%A8%D9%8A';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('نص عربي', $this->request->getParam('arabic'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetClientIp00() {
        $this->request = Request::createFromGlobals();
        $this->assertEquals('127.0.0.1', $this->request->getClientIP());
    }
    /**
     * @test
     */
    public function testGetClientIp01() {
        $_SERVER['REMOTE_ADDR'] = '::1';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('127.0.0.1', $this->request->getClientIP());
    }
    /**
     * @test
     */
    public function testGetClientIp02() {
        $_SERVER['REMOTE_ADDR'] = '127.5.5.6';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('127.5.5.6', $this->request->getClientIP());
    }
    /**
     * @test
     */
    public function testGetClientIp03() {
        $_SERVER['REMOTE_ADDR'] = '127.5A.5.6';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('', $this->request->getClientIP());
    }
    /**
     * @test
     */
    public function testGetRequestedURL00() {
        $_SERVER['HTTP_HOST'] = '127.0.0.1';
        $_SERVER['PATH_INFO'] = '/my/app';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('http://127.0.0.1/my/app', $this->request->getRequestedURI());
    }
    /**
     * @test
     */
    public function testGetRequestedURL01() {
        $_SERVER['PATH_INFO'] = '/my/app';
        $_GET['param1'] = 'something';
        $_GET['param2'] = 'something_else';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('http://127.0.0.1/my/app?param1=something&param2=something_else', $this->request->getRequestedURI());
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetRequestedURL03() {
        putenv('REQUEST_URI=/my/app/x');
        $this->request = Request::createFromGlobals();
        $this->assertEquals('http://127.0.0.1/my/app/x', $this->request->getRequestedURI());
    }
    /**
     * @test
     */
    public function testGetRequestedURL04() {
        putenv('REQUEST_URI=/my/app/x?b=p');
        $_GET['c'] = 'k';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('http://127.0.0.1/my/app/x?c=k', $this->request->getRequestedURI());
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetRequestedURL05() {
        unset($_SERVER['PATH_INFO']);
        putenv('REQUEST_URI');
        putenv('HTTP_REQUEST_URI=/A/B/C');
        $this->request = Request::createFromGlobals();
        $this->assertEquals('http://127.0.0.1/A/B/C', $this->request->getRequestedURI());
    }
    /**
     * @test
     */
    public function testGetRequestedURL06() {
        unset($_SERVER['PATH_INFO']);
        putenv('REQUEST_URI');
        putenv('HTTP_REQUEST_URI');
        $_SERVER['HTTP_X_ORIGINAL_URL'] = 'https://example.com/a/good/boy';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('http://127.0.0.1/a/good/boy', $this->request->getRequestedURI());
        unset($_SERVER['HTTP_X_ORIGINAL_URL']);
    }
    /**
     * @test
     */
    public function testGetHeaders00() {
        // Store original state
        $originalServer = $_SERVER;
        
        // Clear HTTP headers from $_SERVER
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                unset($_SERVER[$key]);
            }
        }
        
        $_SERVER['HTTP_CONTENT_TYPE'] = "application/json";
        $_SERVER['HTTP_X_HOST'] = "Custom H";
        $this->request = Request::createFromGlobals();
        $this->assertEquals([
            'content-type' => [
                'application/json'
            ],
            'x-host' => [
                'Custom H'
            ]
        ], $this->request->getHeadersAssoc());
        
        // Restore original state
        $_SERVER = $originalServer;
    }
    /**
     * @test
     */
    public function testGetCookie00() {
        $this->request = Request::createFromGlobals();
        $this->assertNull($this->request->getCookieValue('not-exist'));
        $_COOKIE['cool'] = 'cool_cookie';
        $this->request = Request::createFromGlobals();
        $this->assertEquals('cool_cookie', $this->request->getCookieValue('cool'));
    }
}
