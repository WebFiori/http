<?php
namespace WebFiori\Tests\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\ErrorResponse;

/**
 * Unit tests for ErrorResponse helper class.
 * 
 * @see https://github.com/WebFiori/http/issues/119
 */
class ErrorResponseTest extends TestCase {

    public function testInvalidParams() {
        $result = ErrorResponse::invalidParams(['email', 'age']);

        $this->assertEquals(404, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(404, $json->get('http-code'));
        $this->assertStringContainsString('email', $json->get('message'));
        $this->assertStringContainsString('age', $json->get('message'));
        $this->assertEquals(['email', 'age'], $json->get('more-info')->get('invalid'));
    }

    public function testInvalidParamsSingle() {
        $result = ErrorResponse::invalidParams(['name']);

        $json = $result['json'];
        $this->assertStringContainsString("'name'", $json->get('message'));
        $this->assertEquals(['name'], $json->get('more-info')->get('invalid'));
    }

    public function testMissingParams() {
        $result = ErrorResponse::missingParams(['username', 'password']);

        $this->assertEquals(404, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(404, $json->get('http-code'));
        $this->assertStringContainsString('username', $json->get('message'));
        $this->assertStringContainsString('password', $json->get('message'));
        $this->assertEquals(['username', 'password'], $json->get('more-info')->get('missing'));
    }

    public function testMissingParamsSingle() {
        $result = ErrorResponse::missingParams(['token']);

        $json = $result['json'];
        $this->assertStringContainsString("'token'", $json->get('message'));
    }

    public function testUnauthorizedDefault() {
        $result = ErrorResponse::unauthorized();

        $this->assertEquals(401, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(401, $json->get('http-code'));
        $this->assertNotEmpty($json->get('message'));
    }

    public function testUnauthorizedCustomMessage() {
        $result = ErrorResponse::unauthorized('You must be a premium member.');

        $json = $result['json'];
        $this->assertEquals('You must be a premium member.', $json->get('message'));
        $this->assertEquals(401, $result['code']);
    }

    public function testMethodNotAllowed() {
        $result = ErrorResponse::methodNotAllowed();

        $this->assertEquals(405, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(405, $json->get('http-code'));
        $this->assertNotEmpty($json->get('message'));
    }

    public function testServiceNotFound() {
        $result = ErrorResponse::serviceNotFound();

        $this->assertEquals(404, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(404, $json->get('http-code'));
        $this->assertNotEmpty($json->get('message'));
    }

    public function testServiceNotImplemented() {
        $result = ErrorResponse::serviceNotImplemented();

        $this->assertEquals(404, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(404, $json->get('http-code'));
        $this->assertNotEmpty($json->get('message'));
    }

    public function testMissingServiceName() {
        $result = ErrorResponse::missingServiceName();

        $this->assertEquals(404, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(404, $json->get('http-code'));
        $this->assertNotEmpty($json->get('message'));
    }

    public function testContentTypeNotSupported() {
        $result = ErrorResponse::contentTypeNotSupported('text/xml');

        $this->assertEquals(415, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(415, $json->get('http-code'));
        $this->assertNotEmpty($json->get('message'));
        $this->assertEquals('text/xml', $json->get('more-info')->get('request-content-type'));
    }

    public function testContentTypeNotSupportedEmpty() {
        $result = ErrorResponse::contentTypeNotSupported('');

        $json = $result['json'];
        $this->assertEquals(415, $json->get('http-code'));
        $this->assertFalse($json->hasKey('more-info'));
    }
}
