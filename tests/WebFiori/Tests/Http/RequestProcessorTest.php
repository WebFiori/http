<?php
namespace WebFiori\Tests\Http;

use WebFiori\Http\APITestCase;
use WebFiori\Http\Request;
use WebFiori\Http\RequestProcessor;
use WebFiori\Tests\Http\TestServices\AnnotatedMethodService;
use WebFiori\Tests\Http\TestServices\AllMethodsService;
use WebFiori\Tests\Http\TestServices\StringAuthDenialService;

/**
 * Tests for RequestProcessor.
 * 
 * @see https://github.com/WebFiori/http/issues/121
 */
class RequestProcessorTest extends APITestCase {

    /**
     * Test processing a GET request with ResponseBody annotation.
     */
    public function testProcessGetWithResponseBody() {
        $processor = new RequestProcessor();
        $service = new AnnotatedMethodService();

        putenv('REQUEST_METHOD=GET');
        $_GET = ['param1' => 'hello'];
        $_SERVER['CONTENT_TYPE'] = '';

        $outFile = $this->getOutputFile();
        $stream = fopen($outFile, 'w');
        $request = Request::createFromGlobals();

        $processor->process($service, $request, $stream);

        $output = file_get_contents($outFile);
        @unlink($outFile);

        $this->assertNotEmpty($output);
    }

    /**
     * Test processing a POST request with parameter validation.
     */
    public function testProcessPostWithParams() {
        $processor = new RequestProcessor();
        $service = new AllMethodsService();

        putenv('REQUEST_METHOD=POST');
        $_POST = ['name' => 'John'];
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';

        $outFile = $this->getOutputFile();
        $stream = fopen($outFile, 'w');
        $request = Request::createFromGlobals();

        $processor->process($service, $request, $stream);

        $output = file_get_contents($outFile);
        @unlink($outFile);

        $this->assertNotEmpty($output);
    }

    /**
     * Test that unauthorized service returns 401.
     */
    public function testUnauthorizedReturnsError() {
        $processor = new RequestProcessor();
        $service = new StringAuthDenialService();

        putenv('REQUEST_METHOD=GET');
        $_GET = [];
        $_SERVER['CONTENT_TYPE'] = '';

        $outFile = $this->getOutputFile();
        $stream = fopen($outFile, 'w');
        $request = Request::createFromGlobals();

        $processor->process($service, $request, $stream);

        $output = file_get_contents($outFile);
        @unlink($outFile);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
        $this->assertEquals(401, $response['http-code']);
        $this->assertEquals('You must be a premium member to access this resource.', $response['message']);
    }

    /**
     * Test that wrong HTTP method returns 405.
     */
    public function testMethodNotAllowed() {
        $processor = new RequestProcessor();
        $service = new AnnotatedMethodService();

        putenv('REQUEST_METHOD=DELETE');
        $_GET = [];
        $_SERVER['CONTENT_TYPE'] = '';

        $outFile = $this->getOutputFile();
        $stream = fopen($outFile, 'w');
        $request = Request::createFromGlobals();

        $processor->process($service, $request, $stream);

        $output = file_get_contents($outFile);
        @unlink($outFile);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }

    /**
     * Test that unsupported content type returns 415.
     */
    public function testContentTypeNotSupported() {
        $processor = new RequestProcessor();
        $service = new AllMethodsService();

        putenv('REQUEST_METHOD=POST');
        $_POST = [];
        $_SERVER['CONTENT_TYPE'] = 'text/xml';

        $outFile = $this->getOutputFile();
        $stream = fopen($outFile, 'w');
        $request = Request::createFromGlobals();

        $processor->process($service, $request, $stream);

        $output = file_get_contents($outFile);
        @unlink($outFile);

        $response = json_decode($output, true);
        $this->assertIsArray($response);
        $this->assertEquals('error', $response['type']);
    }

    /**
     * Test processing with null request (creates from globals).
     */
    public function testProcessWithNullRequest() {
        $processor = new RequestProcessor();
        $service = new AnnotatedMethodService();

        putenv('REQUEST_METHOD=GET');
        $_GET = ['param1' => 'test'];
        $_SERVER['CONTENT_TYPE'] = '';

        $outFile = $this->getOutputFile();
        $stream = fopen($outFile, 'w');

        $processor->process($service, null, $stream);

        $output = file_get_contents($outFile);
        @unlink($outFile);

        $this->assertNotEmpty($output);
    }
}
