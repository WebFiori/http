<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\Request;
use WebFiori\Http\HttpMessage;
use WebFiori\Http\Uri;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for Request class.
 *
 * @author Ibrahim
 */
class RequestTest2 extends TestCase {
    
    private $request;
    
    protected function setUp(): void {
        $this->request = new Request();
    }
    
    /**
     * @test
     */
    public function testExtendsHttpMessage() {
        $this->assertInstanceOf(HttpMessage::class, $this->request);
    }
    
    /**
     * @test
     */
    public function testCreateFromGlobals() {
        putenv('REQUEST_METHOD=POST');
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        
        $request = Request::createFromGlobals();
        
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals('POST', $request->getMethod());
    }
    
    /**
     * @test
     */
    public function testGetMethod() {
        $this->request->setRequestMethod('PUT');
        $this->assertEquals('PUT', $this->request->getMethod());
    }
    
    /**
     * @test
     */
    public function testGetClientIP() {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';
        $this->assertEquals('192.168.1.100', $this->request->getClientIP());
    }
    
    /**
     * @test
     */
    public function testGetClientIPDefault() {
        unset($_SERVER['REMOTE_ADDR']);
        $this->assertEquals('127.0.0.1', $this->request->getClientIP());
    }
    
    /**
     * @test
     */
    public function testGetContentType() {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->assertEquals('application/json', $this->request->getContentType());
    }
    
    /**
     * @test
     */
    public function testGetContentTypeNull() {
        unset($_SERVER['CONTENT_TYPE']);
        $this->assertNull($this->request->getContentType());
    }
    
    /**
     * @test
     */
    public function testGetParamFromGet() {
        putenv('REQUEST_METHOD=GET');
        $_GET['test_param'] = 'test_value';
        
        $request = Request::createFromGlobals();
        $this->assertEquals('test_value', $request->getParam('test_param'));
    }
    
    /**
     * @test
     */
    public function testGetParamFromPost() {
        putenv('REQUEST_METHOD=POST');
        $_POST['test_param'] = 'post_value';
        
        $request = Request::createFromGlobals();
        $this->assertEquals('post_value', $request->getParam('test_param'));
    }
    
    /**
     * @test
     */
    public function testGetParamNotExists() {
        $this->assertNull($this->request->getParam('non_existent'));
    }
    
    /**
     * @test
     */
    public function testGetParams() {
        putenv('REQUEST_METHOD=GET');
        $_GET['param1'] = 'value1';
        $_GET['param2'] = 'value2';
        
        $request = Request::createFromGlobals();
        $params = $request->getParams();
        
        $this->assertArrayHasKey('param1', $params);
        $this->assertArrayHasKey('param2', $params);
        $this->assertEquals('value1', $params['param1']);
        $this->assertEquals('value2', $params['param2']);
    }
    
    /**
     * @test
     */
    public function testGetCookieValue() {
        $_COOKIE['test_cookie'] = 'cookie_value';
        $this->assertEquals('cookie_value', $this->request->getCookieValue('test_cookie'));
    }
    
    /**
     * @test
     */
    public function testGetCookieValueNotExists() {
        $this->assertNull($this->request->getCookieValue('non_existent_cookie'));
    }
    
    /**
     * @test
     */
    public function testGetPath() {
        $_SERVER['REQUEST_URI'] = '/api/users?id=123';
        $request = Request::createFromGlobals();
        $this->assertEquals('/api/users', $request->getPath());
    }
    
    /**
     * @test
     */
    public function testGetPathDefault() {
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['PATH_INFO']);
        unset($_SERVER['SCRIPT_NAME']);
        
        $request = Request::createFromGlobals();
        $this->assertEquals('/', $request->getPath());
    }
    
    /**
     * @test
     */
    public function testGetRequestedURI() {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['HTTP_HOST'] = 'example.com';
        
        $request = Request::createFromGlobals();
        $uri = $request->getRequestedURI();
        
        $this->assertStringContainsString('/test', $uri);
    }
    
    /**
     * @test
     */
    public function testGetRequestedURIWithAppend() {
        $_SERVER['REQUEST_URI'] = '/api';
        $_SERVER['HTTP_HOST'] = 'example.com';
        
        $request = Request::createFromGlobals();
        $uri = $request->getRequestedURI('users');
        
        $this->assertStringContainsString('/api/users', $uri);
    }
    
    /**
     * @test
     */
    public function testGetUri() {
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['HTTP_HOST'] = 'example.com';
        
        $request = Request::createFromGlobals();
        $uri = $request->getUri();
        
        $this->assertInstanceOf(Uri::class, $uri);
    }
    
    /**
     * @test
     */
    public function testGetAuthHeader() {
        $this->request->addHeader('Authorization', 'Bearer token123');
        $authHeader = $this->request->getAuthHeader();
        
        $this->assertNotNull($authHeader);
        $this->assertEquals('bearer', $authHeader->getScheme());
        $this->assertEquals('token123', $authHeader->getCredentials());
    }
    
    /**
     * @test
     */
    public function testGetAuthHeaderNotExists() {
        $this->assertNull($this->request->getAuthHeader());
    }
    
    /**
     * @test
     */
    public function testHeaderExtraction() {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_X_CUSTOM_HEADER'] = 'custom_value';
        
        $request = Request::createFromGlobals();
        
        $this->assertTrue($request->hasHeader('content-type'));
        $this->assertTrue($request->hasHeader('x-custom-header'));
        $this->assertEquals(['application/json'], $request->getHeader('content-type'));
        $this->assertEquals(['custom_value'], $request->getHeader('x-custom-header'));
    }
    
    /**
     * @test
     */
    public function testMultipleInstances() {
        $request1 = new Request();
        $request2 = new Request();
        
        $request1->addHeader('X-Test', 'value1');
        $request2->addHeader('X-Test', 'value2');
        
        $this->assertEquals(['value1'], $request1->getHeader('x-test'));
        $this->assertEquals(['value2'], $request2->getHeader('x-test'));
    }
}
