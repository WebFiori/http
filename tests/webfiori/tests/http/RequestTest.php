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
