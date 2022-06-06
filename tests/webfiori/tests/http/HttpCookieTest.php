<?php
namespace webfiori\tests\http;

use webfiori\http\HttpCookie;
use PHPUnit\Framework\TestCase;
/**
 * Description of HttpCookieTest
 *
 * @author Ibrahim
 */
class HttpCookieTest extends TestCase {
    /**
     * @test
     */
    public function testConstructor00() {
        $cookie = new HttpCookie();
        $this->assertEquals('new-cookie', $cookie->getName());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals('', $cookie->getLifetime());
        $this->assertEquals('Lax', $cookie->getSameSite());
        $cookie->setValue('cool');
        $this->assertEquals('cool', $cookie->getValue());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertTrue($cookie->isSecure());
        $this->assertEquals('new-cookie=cool; path=/; Secure; HttpOnly; SameSite=Lax', $cookie->getHeaderString());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $cookie = new HttpCookie();
        $this->assertEquals('new-cookie', $cookie->getName());
        $cookie->setName('super');
        $this->assertEquals('super', $cookie->getName());
        $this->assertEquals('/', $cookie->getPath());
        $cookie->setPath('/b/a/m');
        $this->assertEquals('/b/a/m', $cookie->getPath());
        $this->assertEquals('', $cookie->getLifetime());
        $cookie->setExpires(10);
        $expires = date(DATE_COOKIE, time() + 10);
        $this->assertEquals($expires, $cookie->getLifetime());
        $this->assertEquals('Lax', $cookie->getSameSite());
        $cookie->setSameSite('Secure');
        $this->assertEquals('Lax', $cookie->getSameSite());
        $cookie->setSameSite('None');
        $this->assertEquals('None', $cookie->getSameSite());
        $cookie->setValue('cool');
        $this->assertEquals('cool', $cookie->getValue());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertTrue($cookie->isSecure());
        $this->assertEquals('super=cool; expires='.$expires.'; path=/b/a/m; Secure; HttpOnly; SameSite=None', $cookie->getHeaderString());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $cookie = new HttpCookie();
        $this->assertEquals('new-cookie', $cookie->getName());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertEquals('', $cookie->getLifetime());
        $this->assertEquals('Lax', $cookie->getSameSite());
        $cookie->setValue('super');
        $this->assertEquals('super', $cookie->getValue());
        $cookie->setIsHttpOnly(false);
        $this->assertFalse($cookie->isHttpOnly());
        $this->assertEquals('new-cookie=super; path=/; Secure; SameSite=Lax', $cookie->getHeaderString());
        $cookie->setIsSecure(false);
        $this->assertFalse($cookie->isSecure());
        $this->assertEquals('new-cookie=super; path=/; SameSite=Lax', $cookie.'');
        $header = $cookie->getHeader();
        $this->assertEquals('set-cookie :new-cookie=super; path=/; SameSite=Lax', $header.'');
    }
}
