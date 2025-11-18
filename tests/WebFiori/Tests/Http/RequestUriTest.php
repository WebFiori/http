<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\RequestUri;
use WebFiori\Http\Uri;
use PHPUnit\Framework\TestCase;
use WebFiori\Http\UriParameter;

/**
 * Test cases for RequestUri class.
 *
 * @author Ibrahim
 */
class RequestUriTest extends TestCase {
    
    /**
     * @test
     */
    public function testExtendsUri() {
        $uri = new RequestUri('http://example.com');
        $this->assertInstanceOf(Uri::class, $uri);
    }
    
    /**
     * @test
     */
    public function testAddRequestMethod() {
        $uri = new RequestUri('http://example.com');
        $uri->addRequestMethod('get');
        $uri->addRequestMethod('POST');
        
        $methods = $uri->getRequestMethods();
        $this->assertContains('GET', $methods);
        $this->assertContains('POST', $methods);
    }
    
    /**
     * @test
     */
    public function testSetRequestMethods() {
        $uri = new RequestUri('http://example.com');
        $uri->setRequestMethods(['get', 'post', 'put']);
        
        $methods = $uri->getRequestMethods();
        $this->assertEquals(['GET', 'POST', 'PUT'], $methods);
    }
    
    /**
     * @test
     */
    public function testIsRequestMethodAllowed() {
        $uri = new RequestUri('http://example.com');
        
        // No methods set - should allow all
        $this->assertTrue($uri->isRequestMethodAllowed('GET'));
        $this->assertTrue($uri->isRequestMethodAllowed('post'));
        
        // Set specific methods
        $uri->setRequestMethods(['GET', 'POST']);
        $this->assertTrue($uri->isRequestMethodAllowed('get'));
        $this->assertTrue($uri->isRequestMethodAllowed('POST'));
        $this->assertFalse($uri->isRequestMethodAllowed('put'));
        $this->assertFalse($uri->isRequestMethodAllowed('DELETE'));
    }
    
    /**
     * @test
     */
    public function testIsRequestMethodAllowedNormalization() {
        $uri = new RequestUri('http://example.com');
        $uri->addRequestMethod('  get  ');
        
        $this->assertTrue($uri->isRequestMethodAllowed('GET'));
        $this->assertTrue($uri->isRequestMethodAllowed('get'));
        $this->assertTrue($uri->isRequestMethodAllowed('  GET  '));
    }
    
    /**
     * @test
     */
    public function testHasParameters() {
        $uri = new RequestUri('https://example.com/users/{id}');
        $this->assertTrue($uri->hasParameters());
        
        $uri2 = new RequestUri('https://example.com/users');
        $this->assertFalse($uri2->hasParameters());
    }
    
    /**
     * @test
     */
    public function testHasParameter() {
        $uri = new RequestUri('https://example.com/users/{id}/posts/{postId}');
        $this->assertTrue($uri->hasParameter('id'));
        $this->assertTrue($uri->hasParameter('postId'));
        $this->assertFalse($uri->hasParameter('name'));
    }
    
    /**
     * @test
     */
    public function testGetParameter() {
        $uri = new RequestUri('https://example.com/users/{id}');
        $param = $uri->getParameter('id');
        
        $this->assertNotNull($param);
        $this->assertEquals('id', $param->getName());
        
        $this->assertNull($uri->getParameter('nonexistent'));
    }
    
    /**
     * @test
     */
    public function testSetParameterValue() {
        $uri = new RequestUri('https://example.com/users/{id}');
        $uri->setParameterValue('id', '123');
        
        $param = $uri->getParameter('id');
        $this->assertEquals('123', $param->getValue());
    }
    
    /**
     * @test
     */
    public function testAddVarValue() {
        $uri = new RequestUri('https://example.com/users/{id}');
        $uri->addAllowedParameterValue('id', '123');
        
        $param = $uri->getParameter('id');
        $this->assertTrue($param instanceof UriParameter);

        $this->assertEquals(['123'], $param->getAllowedValues());
        $this->assertNull($param->getValue());

        $this->assertFalse($param->setValue('132'));
        $this->assertNull($param->getValue());

        $this->assertTrue($param->setValue('123'));
        $this->assertEquals('123', $param->getValue());
    }
    
    /**
     * @test
     */
    public function testEquals() {
        $uri1 = new RequestUri('https://example.com/users/{id}');
        $uri1->setRequestMethods(['GET', 'POST']);
        
        $uri2 = new RequestUri('https://example.com/users/{id}');
        $uri2->setRequestMethods(['GET', 'POST']);
        
        $uri3 = new RequestUri('https://example.com/users/{name}');
        $uri3->setRequestMethods(['GET', 'POST']);
        
        $this->assertTrue($uri1->equals($uri2));
        $this->assertFalse($uri1->equals($uri3));
    }
    
    /**
     * @test
     */
    public function testIsAllParametersSet() {
        $uri = new RequestUri('https://example.com/users/{id}/posts/{postId}');
        $this->assertFalse($uri->isAllParametersSet());
        
        $uri->setParameterValue('id', '123');
        $this->assertFalse($uri->isAllParametersSet());
        
        $uri->setParameterValue('postId', '456');
        $this->assertTrue($uri->isAllParametersSet());
    }
    
    /**
     * @test
     */
    public function testGetParameterValues() {
        $uri = new RequestUri('https://example.com/users/{id}/posts/{postId}');
        $uri->setParameterValue('id', '123');
        $uri->setParameterValue('postId', '789');
        
        $value0 = $uri->getParameterValue('id');
        $value1 = $uri->getParameterValue('postId');
        $this->assertEquals('123', $value0);
        $this->assertEquals('789', $value1);
    }
    /**
     * @test
     */
    public function testSetUriPossibleVar03() {
        $uri = new RequestUri('https://example.com/{first-var}/ok/{second-var}');
        $uri->addAllowedParameterValues('first-var', ['Hello','World'])
        ->addAllowedParameterValues('  second-var ', ['hell','is','not','heven'])
        ->addAllowedParameterValues('  secohhnd-var ', ['hell','is']);
        $this->assertEquals(['Hello','World'], $uri->getAllowedParameterValues('first-var'));
        $this->assertEquals(['hell','is','not','heven'], $uri->getAllowedParameterValues('second-var'));
        $this->assertEquals([], $uri->getAllowedParameterValues('secohhnd-var'));
    }
}
