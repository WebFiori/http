<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\HttpMessage;
use WebFiori\Http\HeadersPool;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for HttpMessage class.
 *
 * @author Ibrahim
 */
class HttpMessageTest extends TestCase {
    
    private $message;
    
    protected function setUp(): void {
        $this->message = new HttpMessage();
    }
    
    /**
     * @test
     */
    public function testConstructor() {
        $message = new HttpMessage();
        $this->assertInstanceOf(HeadersPool::class, $message->getHeadersPool());
    }
    
    /**
     * @test
     */
    public function testGetHeadersPool() {
        $pool = $this->message->getHeadersPool();
        $this->assertInstanceOf(HeadersPool::class, $pool);
    }
    
    /**
     * @test
     */
    public function testAddHeader() {
        $result = $this->message->addHeader('Content-Type', 'application/json');
        $this->assertTrue($result);
        
        $headers = $this->message->getHeaders();
        $this->assertCount(1, $headers);
        $this->assertEquals('content-type', $headers[0]->getName());
        $this->assertEquals('application/json', $headers[0]->getValue());
    }
    
    /**
     * @test
     */
    public function testAddInvalidHeader() {
        $result = $this->message->addHeader('Invalid Header Name', 'value');
        $this->assertFalse($result);
    }
    
    /**
     * @test
     */
    public function testGetHeader() {
        $this->message->addHeader('Content-Type', 'application/json');
        
        $values = $this->message->getHeader('content-type');
        $this->assertEquals(['application/json'], $values);
        
        $empty = $this->message->getHeader('non-existent');
        $this->assertEquals([], $empty);
    }
    
    /**
     * @test
     */
    public function testGetHeaders() {
        $this->assertEquals([], $this->message->getHeaders());
        
        $this->message->addHeader('Content-Type', 'application/json');
        $this->message->addHeader('Accept', 'text/html');
        
        $headers = $this->message->getHeaders();
        $this->assertCount(2, $headers);
    }
    
    /**
     * @test
     */
    public function testHasHeader() {
        $this->assertFalse($this->message->hasHeader('Content-Type'));
        
        $this->message->addHeader('Content-Type', 'application/json');
        
        $this->assertTrue($this->message->hasHeader('Content-Type'));
        $this->assertTrue($this->message->hasHeader('content-type'));
        $this->assertTrue($this->message->hasHeader('Content-Type', 'application/json'));
        $this->assertFalse($this->message->hasHeader('Content-Type', 'text/html'));
    }
    
    /**
     * @test
     */
    public function testRemoveHeader() {
        $this->message->addHeader('Content-Type', 'application/json');
        $this->message->addHeader('Accept', 'text/html');
        
        $this->assertTrue($this->message->hasHeader('Content-Type'));
        
        $result = $this->message->removeHeader('Content-Type');
        $this->assertTrue($result);
        $this->assertFalse($this->message->hasHeader('Content-Type'));
        $this->assertTrue($this->message->hasHeader('Accept'));
        
        $result = $this->message->removeHeader('non-existent');
        $this->assertFalse($result);
    }
    
    /**
     * @test
     */
    public function testRemoveHeaderWithValue() {
        $this->message->addHeader('Accept', 'application/json');
        $this->message->addHeader('Accept', 'text/html');
        
        $result = $this->message->removeHeader('Accept', 'application/json');
        $this->assertTrue($result);
        
        $values = $this->message->getHeader('Accept');
        $this->assertEquals(['text/html'], $values);
    }
    
    /**
     * @test
     */
    public function testMultipleHeaderValues() {
        $this->message->addHeader('Accept', 'application/json');
        $this->message->addHeader('Accept', 'text/html');
        
        $values = $this->message->getHeader('Accept');
        $this->assertCount(2, $values);
        $this->assertContains('application/json', $values);
        $this->assertContains('text/html', $values);
    }
    
    /**
     * @test
     */
    public function testReplaceHeaderValue() {
        $this->message->addHeader('Content-Type', 'text/plain');
        $this->message->addHeader('Content-Type', 'application/json', 'text/plain');
        
        $values = $this->message->getHeader('Content-Type');
        $this->assertEquals(['application/json'], $values);
    }
    
    /**
     * @test
     */
    public function testCaseInsensitiveHeaders() {
        $this->message->addHeader('Content-Type', 'application/json');
        
        $this->assertTrue($this->message->hasHeader('content-type'));
        $this->assertTrue($this->message->hasHeader('CONTENT-TYPE'));
        $this->assertTrue($this->message->hasHeader('Content-Type'));
        
        $values = $this->message->getHeader('CONTENT-TYPE');
        $this->assertEquals(['application/json'], $values);
    }
    
    /**
     * @test
     */
    public function testMultipleInstances() {
        $message1 = new HttpMessage();
        $message2 = new HttpMessage();
        
        $message1->addHeader('Content-Type', 'application/json');
        $message2->addHeader('Accept', 'text/html');
        
        $this->assertTrue($message1->hasHeader('Content-Type'));
        $this->assertFalse($message1->hasHeader('Accept'));
        
        $this->assertTrue($message2->hasHeader('Accept'));
        $this->assertFalse($message2->hasHeader('Content-Type'));
    }
    
    public function testGetBody() {
        $message = new \WebFiori\Http\Response();
        $message->write('test body');
        $this->assertEquals('test body', $message->getBody());
    }
}

