<?php

namespace webfiori\tests\http;

use PHPUnit\Framework\TestCase;
use webfiori\http\HttpCookie;
use webfiori\http\Response;
use webfiori\tests\http\testServices\TestUserObj;

/**
 * Description of RequestTest
 *
 * @author Ibrahim
 */

class ResponseTest extends TestCase {
    /**
     * @test
     */
    public function testAddHeader00() {
        $this->assertFalse(Response::hasHeader('content-type', null));
        $this->assertTrue(Response::addHeader('content-type', 'application/json'));
        $this->assertTrue(Response::hasHeader('content-type', 'application/json'));
        $this->assertFalse(Response::hasHeader('content-type', 'text/js'));
    }
    /**
     * @test
     */
    public function testAddHeader01() {
        $this->assertFalse(Response::hasHeader('Set-Cookie', null));
        $this->assertTrue(Response::addHeader('Set-Cookie', 'name=ok'));
        $this->assertTrue(Response::hasHeader('Set-Cookie', null));
        $this->assertTrue(Response::hasHeader('Set-Cookie','name=ok'));
        
        $this->assertTrue(Response::addHeader('Set-Cookie', 'name=good'));
        $this->assertTrue(Response::hasHeader('Set-cookie','name=good'));
        
        $this->assertTrue(Response::addHeader('Set-Cookie', 'name=no'));
        $this->assertTrue(Response::hasHeader('Set-Cookie','name=no'));
        $headerArr = Response::getHeader('set-cookie');
        $this->assertEquals([
            'name=ok',
            'name=good',
            'name=no'
        ], $headerArr);
        
    }
    /**
     * @test
     * @depends testRemoveHeader01
     */
    public function testAddHeader02() {
        Response::clear();
        $this->assertEquals([], Response::getHeaders());
        Response::addHeader('set-cookie', 'name=super');
        $this->assertEquals([
            'name=super'
        ], Response::getHeader('set-cookie'));
        Response::addHeader('set-cookie', 'name=not-super', 'name=super');
        $this->assertEquals([
            'name=not-super'
        ], Response::getHeader('set-cookie'));
    }
    /**
     * @test
     * @depends testAddHeader00
     */
    public function testRemoveHeader00() {
        $this->assertTrue(Response::hasHeader('content-type'));
        Response::removeHeader('content-type');
        $this->assertFalse(Response::hasHeader('content-type'));
        $headerArr = Response::getHeader('content-type');
        $this->assertEquals([], $headerArr);
    }
    /**
     * @test
     * @depends testAddHeader01
     */
    public function testRemoveHeader01() { 
        $this->assertTrue(Response::hasHeader('Set-Cookie'));
        $this->assertTrue(Response::removeHeader('Set-cookie', 'name=good'));
        $this->assertFalse(Response::hasHeader('Set-cookie','name=good'));
        $this->assertTrue(Response::hasHeader('Set-Cookie','name=no'));
        $this->assertTrue(Response::hasHeader('Set-Cookie','name=ok'));
        Response::removeHeader('Set-cookie');
        $this->assertFalse(Response::hasHeader('Set-Cookie'));
    }
    /**
     * @test
     */
    public function testRemoveHeaders() {
        Response::addHeader('content-type', 'application/json');
        $this->assertTrue(Response::hasHeader('content-type'));
        $this->assertFalse(Response::hasHeader('content-type','text/plain'));
        Response::clearHeaders();
        $this->assertEquals(0, count(Response::getHeaders()));
    }
    /**
     * @test
     */
    public function testClearBody() {
        Response::write('Hello World!');
        $this->assertEquals('Hello World!', Response::getBody());
        Response::clearBody();
        $this->assertEquals('', Response::getBody());
    }
    /**
     * @test
     */
    public function testSetResponseCode() {
        $this->assertEquals(200, Response::getCode());
        Response::setCode(99);
        $this->assertEquals(200, Response::getCode());
        Response::setCode(100);
        $this->assertEquals(100, Response::getCode());
        Response::setCode(599);
        $this->assertEquals(599, Response::getCode());
        Response::setCode(600);
        $this->assertEquals(599, Response::getCode());
    }
    /**
     * @test
     */
    public function testBeforeSend00() {
        $this->assertFalse(Response::hasHeader('super', null));
        Response::beforeSend(function () {
            Response::addHeader('super', 'yes');
        });
        $this->assertFalse(Response::isSent());
        Response::send();
        $this->assertTrue(Response::hasHeader('super'));
        Response::clearHeaders();
        $headerArr = Response::getHeaders();
        $this->assertEquals([], $headerArr);
    }
    /**
     * @test
     */
    public function testCookies00() {
        $this->assertFalse(Response::hasCookie('cool'));
        $this->assertEquals([], Response::getCookies());
        $this->assertNull(Response::getCookie('cool'));
        $coolCookie = new HttpCookie();
        $coolCookie->setName('cool');
        Response::addCookie($coolCookie);
        $this->assertTrue(Response::hasCookie('cool'));
        $this->assertEquals([$coolCookie], Response::getCookies());
        $this->assertNotNull(Response::getCookie('cool'));
    }
    /**
     * @test
     */
    public function testDump00() {
        $bool = true;
        Response::clear();
        Response::write($bool);
        $this->assertEquals(""
                . "<pre>"
                . "bool(true)\n"
                . "</pre>", Response::getBody());
    }
    /**
     * @test
     */
    public function testDump01() {
        $null = null;
        Response::clear();
        Response::write($null);
        $this->assertEquals(""
                . "<pre>"
                . "NULL\n"
                . "</pre>", Response::getBody());
    }
    /**
     * @test
     */
    public function testDump02() {
        Response::clear();
        Response::write([1,2,3]);
        $this->assertEquals(""
                . "<pre>array(3) {\n"
                . "  [0]=>\n"
                . "  int(1)\n"
                . "  [1]=>\n"
                . "  int(2)\n"
                . "  [2]=>\n"
                . "  int(3)\n"
                . "}\n</pre>", Response::getBody());
    }
    /**
     * @test
     */
    public function testDump03() {
        $null = null;
        Response::clear();
        Response::dump(61);
        $this->assertEquals(""
                . "<pre>"
                . "int(61)\n"
                . "</pre>", Response::getBody());
    }
}
