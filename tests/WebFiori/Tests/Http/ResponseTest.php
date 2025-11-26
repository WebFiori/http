<?php

namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\HttpCookie;
use WebFiori\Http\Response;

/**
 * Test cases for Response class
 *
 * @author Ibrahim
 */
class ResponseTest extends TestCase {
    /**
     * @test
     */
    public function testAddHeader00() {
        $response = new Response();
        $this->assertFalse($response->hasHeader('content-type', null));
        $this->assertTrue($response->addHeader('content-type', 'application/json'));
        $this->assertTrue($response->hasHeader('content-type', 'application/json'));
        $this->assertFalse($response->hasHeader('content-type', 'text/js'));
    }

    /**
     * @test
     */
    public function testAddHeader01() {
        $response = new Response();
        $this->assertFalse($response->hasHeader('Set-Cookie', null));
        $this->assertTrue($response->addHeader('Set-Cookie', 'name=ok'));
        $this->assertTrue($response->hasHeader('Set-Cookie', null));
        $this->assertTrue($response->hasHeader('Set-Cookie','name=ok'));
        
        $this->assertTrue($response->addHeader('Set-Cookie', 'name=good'));
        $this->assertTrue($response->hasHeader('Set-cookie','name=good'));
        
        $this->assertTrue($response->addHeader('Set-Cookie', 'name=no'));
        $this->assertTrue($response->hasHeader('Set-Cookie','name=no'));
        $headerArr = $response->getHeader('set-cookie');
        $this->assertEquals([
            'name=ok',
            'name=good',
            'name=no'
        ], $headerArr);
    }

    /**
     * @test
     */
    public function testAddHeader02() {
        $response = new Response();
        $this->assertEquals([], $response->getHeaders());
        $response->addHeader('set-cookie', 'name=super');
        $this->assertEquals([
            'name=super'
        ], $response->getHeader('set-cookie'));
        $response->addHeader('set-cookie', 'name=not-super', 'name=super');
        $this->assertEquals([
            'name=not-super'
        ], $response->getHeader('set-cookie'));
    }

    /**
     * @test
     */
    public function testRemoveHeader00() {
        $response = new Response();
        $response->addHeader('content-type', 'application/json');
        $this->assertTrue($response->hasHeader('content-type'));
        $response->removeHeader('content-type');
        $this->assertFalse($response->hasHeader('content-type'));
        $headerArr = $response->getHeader('content-type');
        $this->assertEquals([], $headerArr);
    }

    /**
     * @test
     */
    public function testRemoveHeader01() {
        $response = new Response();
        $response->addHeader('Set-Cookie', 'name=ok');
        $response->addHeader('Set-Cookie', 'name=good');
        $response->addHeader('Set-Cookie', 'name=no');
        
        $this->assertTrue($response->hasHeader('Set-Cookie'));
        $this->assertTrue($response->removeHeader('Set-cookie', 'name=good'));
        $this->assertFalse($response->hasHeader('Set-cookie','name=good'));
        $this->assertTrue($response->hasHeader('Set-Cookie','name=no'));
        $this->assertTrue($response->hasHeader('Set-Cookie','name=ok'));
        $response->removeHeader('Set-cookie');
        $this->assertFalse($response->hasHeader('Set-Cookie'));
    }

    /**
     * @test
     */
    public function testRemoveHeaders() {
        $response = new Response();
        $response->addHeader('content-type', 'application/json');
        $this->assertTrue($response->hasHeader('content-type'));
        $this->assertFalse($response->hasHeader('content-type','text/plain'));
        $response->clearHeaders();
        $this->assertEquals(0, count($response->getHeaders()));
    }

    /**
     * @test
     */
    public function testClearBody() {
        $response = new Response();
        $response->write('Hello World!');
        $this->assertEquals('Hello World!', $response->getBody());
        $response->clearBody();
        $this->assertEquals('', $response->getBody());
    }

    /**
     * @test
     */
    public function testSetResponseCode() {
        $response = new Response();
        $this->assertEquals(200, $response->getCode());
        $response->setCode(99);
        $this->assertEquals(200, $response->getCode());
        $response->setCode(100);
        $this->assertEquals(100, $response->getCode());
        $response->setCode(599);
        $this->assertEquals(599, $response->getCode());
        $response->setCode(600);
        $this->assertEquals(599, $response->getCode());
    }

    /**
     * @test
     */
    public function testBeforeSend00() {
        $response = new Response();
        $this->assertFalse($response->hasHeader('super', null));
        $response->beforeSend(function () use ($response) {
            $response->addHeader('super', 'yes');
        });
        $this->assertFalse($response->isSent());
        $response->send();
        $this->assertTrue($response->hasHeader('super'));
    }

    /**
     * @test
     */
    public function testCookies00() {
        $response = new Response();
        $this->assertFalse($response->hasCookie('cool'));
        $this->assertEquals([], $response->getCookies());
        $this->assertNull($response->getCookie('cool'));
        $coolCookie = new HttpCookie();
        $coolCookie->setName('cool');
        $response->addCookie($coolCookie);
        $this->assertTrue($response->hasCookie('cool'));
        $this->assertEquals([$coolCookie], $response->getCookies());
        $this->assertNotNull($response->getCookie('cool'));
    }

    /**
     * @test
     */
    public function testDump00() {
        $response = new Response();
        $bool = true;
        $response->clear();
        $response->write($bool);
        $this->assertStringContainsString("bool(true)", $response->getBody());
        $this->assertStringStartsWith("<pre>", $response->getBody());
        $this->assertStringEndsWith("</pre>", $response->getBody());
    }

    /**
     * @test
     */
    public function testDump01() {
        $response = new Response();
        $null = null;
        $response->clear();
        $response->write($null);
        $this->assertStringContainsString("NULL", $response->getBody());
        $this->assertStringStartsWith("<pre>", $response->getBody());
        $this->assertStringEndsWith("</pre>", $response->getBody());
    }

    /**
     * @test
     */
    public function testDump02() {
        $response = new Response();
        $response->clear();
        $response->write([1,2,3]);
        $body = $response->getBody();
        $this->assertStringContainsString("array(3)", $body);
        $this->assertStringContainsString("int(1)", $body);
        $this->assertStringContainsString("int(2)", $body);
        $this->assertStringContainsString("int(3)", $body);
        $this->assertStringStartsWith("<pre>", $body);
        $this->assertStringEndsWith("</pre>", $body);
    }

    /**
     * @test
     */
    public function testDump03() {
        $response = new Response();
        $response->clear();
        $response->dump(61);
        $this->assertStringContainsString("int(61)", $response->getBody());
        $this->assertStringStartsWith("<pre>", $response->getBody());
        $this->assertStringEndsWith("</pre>", $response->getBody());
    }

    /**
     * @test
     */
    public function testClear() {
        $response = new Response();
        $response->addHeader('content-type', 'application/json');
        $response->write('test body');
        $this->assertNotEquals('', $response->getBody());
        $this->assertNotEquals([], $response->getHeaders());
        
        $response->clear();
        $this->assertEquals('', $response->getBody());
        $this->assertEquals([], $response->getHeaders());
    }
}
