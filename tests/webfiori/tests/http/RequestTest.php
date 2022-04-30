<?php
namespace webfiori\tests\http;

use webfiori\http\Request;
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
    }
    /**
     * @test
     */
    public function testGetParam06() {
        putenv('REQUEST_METHOD=GET');
        $_GET['hello'] = 'This+is+an+encoded%20string.';
        $this->assertEquals('This is an encoded string.', Request::getParam('hello'));
    }
    /**
     * @test
     */
    public function testGetParams06() {
        putenv('REQUEST_METHOD=GET');
        $_GET['arabic'] = '%D9%86%D8%B5%20%D8%B9%D8%B1%D8%A8%D9%8A';
        $this->assertEquals('نص عربي', Request::getParam('arabic'));
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
        $this->assertEquals('http://127.0.0.1/my/app', Request::getRequestedURL());
    }
    
}
