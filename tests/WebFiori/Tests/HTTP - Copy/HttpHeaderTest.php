<?php
namespace webfiori\tests\http;

use webfiori\http\HttpHeader;
use PHPUnit\Framework\TestCase;
/**
 * Description of HttpHeaderTest
 *
 * @author Ibrahim
 */
class HttpHeaderTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $header = new HttpHeader();
        $this->assertEquals('http-header', $header->getName());
        $this->assertEquals('', $header->getValue());
        $this->assertEquals('http-header: ', $header);
    }
    /**
     * @test
     */
    public function test01() {
        $header = new HttpHeader('User-Agent', 'Chrome');
        $this->assertEquals('user-agent', $header->getName());
        $this->assertEquals('Chrome', $header->getValue());
        $this->assertEquals('user-agent: Chrome', $header);
    }
    /**
     * @test
     */
    public function test02() {
        $header = new HttpHeader('User Agent', 'Chrome');
        $this->assertEquals('http-header', $header->getName());
        $this->assertEquals('Chrome', $header->getValue());
        $this->assertEquals('http-header: Chrome', $header);
    }
}
