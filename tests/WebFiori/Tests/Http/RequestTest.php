<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Request;
use PHPUnit\Framework\TestCase;
/**
 * Description of RequestTest
 *
 * @author Ibrahim
 */
class RequestTest extends TestCase {
    /**
     * 
     */
    public function testGetParam00() {
        $this->assertNull(Request::getParam('not-exist'));
    }
    /**
     * @test
     */
    public function testGetParam01() {
        putenv('REQUEST_METHOD=POST');
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
        $_POST['ok'] = 'I Ok';
        $this->assertEquals('I Ok', Request::getParam('ok'));
    }
    /**
     * @test
     */
    public function testGetParam02() {
        putenv('REQUEST_METHOD=GET');
        $_GET['ok'] = 'I not Ok';
        $this->assertEquals('I not Ok', Request::getParam('ok'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetParam03() {
        putenv('REQUEST_METHOD=POST');
        $this->assertNull(Request::getParam('not-exist'));
    }
    /**
     * @test
     */
    public function testGetParam04() {
        putenv('REQUEST_METHOD=GET');
        $this->assertNull(Request::getParam('not-exist'));
    }
    /**
     * @test
     */
    public function testGetParam05() {
        putenv('REQUEST_METHOD=GET');
        $_GET['hello'] = 'This%20is%20an%20encoded%20string.';
        $this->assertEquals('This is an encoded string.', Request::getParam('hello'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetParam06() {
        putenv('REQUEST_METHOD=GET');
        $_GET['hello'] = 'This+is+an+encoded%20string.';
        $this->assertEquals('This is an encoded string.', Request::getParam('hello'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetParams06() {
        putenv('REQUEST_METHOD=GET');
        $_GET['arabic'] = '%D9%86%D8%B5%20%D8%B9%D8%B1%D8%A8%D9%8A';
        $this->assertEquals('نص عربي', Request::getParam('arabic'));
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetClientIp00() {
        $this->assertEquals('127.0.0.1', Request::getClientIP());
    }
    /**
     * @test
     */
    public function testGetClientIp01() {
        $_SERVER['REMOTE_ADDR'] = '::1';
        $this->assertEquals('127.0.0.1', Request::getClientIP());
    }
    /**
     * @test
     */
    public function testGetClientIp02() {
        $_SERVER['REMOTE_ADDR'] = '127.5.5.6';
        $this->assertEquals('127.5.5.6', Request::getClientIP());
    }
    /**
     * @test
     */
    public function testGetClientIp03() {
        $_SERVER['REMOTE_ADDR'] = '127.5A.5.6';
        $this->assertEquals('', Request::getClientIP());
    }
    /**
     * @test
     */
    public function testGetRequestedURL00() {
        $_SERVER['PATH_INFO'] = '/my/app';
        $this->assertEquals('http://127.0.0.1/my/app', Request::getRequestedURI());
    }
    /**
     * @test
     */
    public function testGetRequestedURL01() {
        $_SERVER['PATH_INFO'] = '/my/app';
        $_GET['param1'] = 'something';
        $_GET['param2'] = 'something_else';
        $this->assertEquals('http://127.0.0.1/my/app?param1=something&param2=something_else', Request::getRequestedURI());
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetRequestedURL03() {
        putenv('REQUEST_URI=/my/app/x');
        $this->assertEquals('http://127.0.0.1/my/app/x', Request::getRequestedURI());
    }
    /**
     * @test
     */
    public function testGetRequestedURL04() {
        putenv('REQUEST_URI=/my/app/x?b=p');
        $_GET['c'] = 'k';
        $this->assertEquals('http://127.0.0.1/my/app/x?c=k', Request::getRequestedURI());
        $_GET = [];
    }
    /**
     * @test
     */
    public function testGetRequestedURL05() {
        putenv('REQUEST_URI');
        putenv('HTTP_REQUEST_URI=/A/B/C');
        $this->assertEquals('http://127.0.0.1/A/B/C', Request::getRequestedURI());
    }
    /**
     * @test
     */
    public function testGetRequestedURL06() {
        putenv('REQUEST_URI');
        putenv('HTTP_REQUEST_URI');
        $_SERVER['HTTP_X_ORIGINAL_URL'] = 'https://example.com/a/good/boy';
        $this->assertEquals('http://127.0.0.1/a/good/boy', Request::getRequestedURI());
        unset($_SERVER['HTTP_X_ORIGINAL_URL']);
    }
    /**
     * @test
     */
    public function testGetHeaders00() {
        $_SERVER['HTTP_CONTENT_TYPE'] = "application/json";
        $_SERVER['HTTP_X_HOST'] = "Custom H";
        $this->assertEquals([
            'content-type' => [
                'application/json'
            ],
            'x-host' => [
                'Custom H'
            ]
        ], Request::getHeadersAssoc());
    }
    /**
     * @test
     */
    public function testGetCookie00() {
        $this->assertNull(Request::getCookieValue('not-exist'));
        $_COOKIE['cool'] = 'cool_cookie';
        $this->assertEquals('cool_cookie', Request::getCookieValue('cool'));
    }
}
