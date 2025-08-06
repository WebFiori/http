<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\HttpCookie;
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
        $cookie->setDomain('');
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
        $cookie->setDomain();
        $this->assertEquals('new-cookie', $cookie->getName());
        $cookie->setName('super');
        $this->assertEquals('super', $cookie->getName());
        $this->assertEquals('/', $cookie->getPath());
        $cookie->setPath('/b/a/m');
        $this->assertEquals('/b/a/m', $cookie->getPath());
        $this->assertEquals('', $cookie->getLifetime());
        $cookie->setExpires(10);
        $expires = date(DATE_COOKIE, time() + 10*60);
        $this->assertTrue($cookie->isPersistent());
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
        $cookie->setExpires(-1);
        $expires = date(DATE_COOKIE, time() - 1*60);
        $this->assertEquals(time() - 1*60, $cookie->getExpires());
        $this->assertEquals($expires, $cookie->getLifetime());
        $this->assertEquals('super=cool; expires='.$expires.'; path=/b/a/m; Secure; HttpOnly; SameSite=None', $cookie->getHeaderString());
        $cookie->setExpires(0);
        $this->assertFalse($cookie->isPersistent());
        $this->assertEquals(0, $cookie->getExpires());
        $this->assertEquals('super=cool; path=/b/a/m; Secure; HttpOnly; SameSite=None', $cookie->getHeaderString());
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
        $this->assertEquals('127.0.0.1', $cookie->getDomain());
        $cookie->setValue('super');
        $this->assertEquals('super', $cookie->getValue());
        $cookie->setIsHttpOnly(false);
        $this->assertFalse($cookie->isHttpOnly());
        $this->assertEquals('new-cookie=super; domain=127.0.0.1; path=/; Secure; SameSite=Lax', $cookie->getHeaderString());
        $cookie->setIsSecure(false);
        $this->assertFalse($cookie->isSecure());
        $cookie->setDomain();
        $this->assertEquals('new-cookie=super; path=/; SameSite=Lax', $cookie.'');
        
        $this->assertEquals('set-cookie: new-cookie=super; path=/; SameSite=Lax', $cookie->getHeader().'');
        $cookie->setDomain('webfiori.com');
        $this->assertEquals('set-cookie: new-cookie=super; domain=webfiori.com; path=/; SameSite=Lax', $cookie->getHeader().'');
        $cookie->kill();
        $this->assertEquals(date(DATE_COOKIE, time() - 60*60*24), $cookie->getLifetime());
    }
    /**
     * @test
     */
    public function testRemainingTime00() {
        $cookie = new HttpCookie();
        $this->assertEquals(0, $cookie->getRemainingTime());
        $cookie->setExpires(1);
        $this->assertEquals(60, $cookie->getRemainingTime());
        sleep(3);
        $this->assertEquals(57, $cookie->getRemainingTime());
    }
    /**
     * @test
     */
    public function testRemainingTime01() {
        $cookie = new HttpCookie();
        $cookie->setExpires(0.1);
        $this->assertEquals(6, $cookie->getRemainingTime());
        sleep(8);
        $this->assertEquals(0, $cookie->getRemainingTime());
    }
}
