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

        $this->assertEquals(422, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(422, $json->get('http-code'));
        $this->assertEquals('Validation failed', $json->get('message'));
        $errors = $json->get('more-info')->get('errors');
        $this->assertStringContainsString('email', $errors->get('email'));
        $this->assertStringContainsString('age', $errors->get('age'));
    }

    public function testInvalidParamsSingle() {
        $result = ErrorResponse::invalidParams(['name']);

        $json = $result['json'];
        $errors = $json->get('more-info')->get('errors');
        $this->assertStringContainsString('name', $errors->get('name'));
    }

    public function testMissingParams() {
        $result = ErrorResponse::missingParams(['username', 'password']);

        $this->assertEquals(422, $result['code']);
        $json = $result['json'];
        $this->assertEquals('error', $json->get('type'));
        $this->assertEquals(422, $json->get('http-code'));
        $this->assertEquals('Validation failed', $json->get('message'));
        $errors = $json->get('more-info')->get('errors');
        $this->assertStringContainsString('username', $errors->get('username'));
        $this->assertStringContainsString('password', $errors->get('password'));
    }

    public function testMissingParamsSingle() {
        $result = ErrorResponse::missingParams(['token']);

        $json = $result['json'];
        $errors = $json->get('more-info')->get('errors');
        $this->assertStringContainsString('token', $errors->get('token'));
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
