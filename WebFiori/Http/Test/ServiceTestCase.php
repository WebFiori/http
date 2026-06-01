<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http\Test;

use PHPUnit\Framework\TestCase;
use WebFiori\Http\Request;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\RequestProcessor;
use WebFiori\Http\SecurityContext;
use WebFiori\Http\SecurityPrincipal;
use WebFiori\Http\WebService;

/**
 * Base test case for testing web services directly.
 * 
 * Provides a clean API for sending requests to a service and asserting
 * on the response using fluent TestResponse assertions.
 * 
 * Usage:
 * ```php
 * class MyServiceTest extends ServiceTestCase {
 *     public function testGetItems() {
 *         $this->get(new ItemService(), ['page' => 1])
 *             ->assertOk()
 *             ->assertJsonHas('items');
 *     }
 * }
 * ```
 *
 * @author Ibrahim
 */
class ServiceTestCase extends TestCase {
    private array $globalsBackup;

    protected function setUp(): void {
        parent::setUp();
        $this->globalsBackup = [
            'GET' => $_GET,
            'POST' => $_POST,
            'FILES' => $_FILES,
            'SERVER' => $_SERVER,
        ];
    }

    protected function tearDown(): void {
        $_GET = $this->globalsBackup['GET'];
        $_POST = $this->globalsBackup['POST'];
        $_FILES = $this->globalsBackup['FILES'];
        $_SERVER = $this->globalsBackup['SERVER'];
        SecurityContext::clear();
        parent::tearDown();
    }
    /**
     * Send a request to a service with a specific HTTP method.
     * 
     * @param string $method HTTP method (GET, POST, PUT, PATCH, DELETE).
     * @param WebService $service The service to test.
     * @param array $params Request parameters.
     * @param SecurityPrincipal|null $user Authenticated user, or null for anonymous.
     * @param array $headers HTTP headers as key-value pairs.
     * 
     * @return TestResponse
     */
    protected function call(string $method, WebService $service, array $params = [], ?SecurityPrincipal $user = null, array $headers = []): TestResponse {
        $method = strtoupper($method);
        $this->setupGlobals($method, $params, $headers);

        $outFile = tempnam(sys_get_temp_dir(), 'svc_test_');
        $stream = fopen($outFile, 'w');
        SecurityContext::setCurrentUser($user);
        $request = Request::createFromGlobals();

        $processor = new RequestProcessor();
        $processor->process($service, $request, $stream);

        $body = file_get_contents($outFile);
        @unlink($outFile);

        return new TestResponse($body);
    }
    /**
     * Send a GET request to a service.
     * 
     * @return TestResponse
     */
    protected function get(WebService $service, array $params = [], ?SecurityPrincipal $user = null, array $headers = []): TestResponse {
        return $this->call(RequestMethod::GET, $service, $params, $user, $headers);
    }
    /**
     * Send a POST request to a service.
     * 
     * @return TestResponse
     */
    protected function post(WebService $service, array $params = [], ?SecurityPrincipal $user = null, array $headers = []): TestResponse {
        return $this->call(RequestMethod::POST, $service, $params, $user, $headers);
    }
    /**
     * Send a PUT request to a service.
     * 
     * @return TestResponse
     */
    protected function put(WebService $service, array $params = [], ?SecurityPrincipal $user = null, array $headers = []): TestResponse {
        return $this->call(RequestMethod::PUT, $service, $params, $user, $headers);
    }
    /**
     * Send a PATCH request to a service.
     * 
     * @return TestResponse
     */
    protected function patch(WebService $service, array $params = [], ?SecurityPrincipal $user = null, array $headers = []): TestResponse {
        return $this->call(RequestMethod::PATCH, $service, $params, $user, $headers);
    }
    /**
     * Send a DELETE request to a service.
     * 
     * @return TestResponse
     */
    protected function delete(WebService $service, array $params = [], ?SecurityPrincipal $user = null, array $headers = []): TestResponse {
        return $this->call(RequestMethod::DELETE, $service, $params, $user, $headers);
    }

    private function setupGlobals(string $method, array $params, array $headers): void {
        $normalizedHeaders = [];

        foreach ($headers as $name => $value) {
            $normalizedHeaders[strtolower($name)] = $value;
        }

        if (in_array($method, [RequestMethod::POST, RequestMethod::PUT, RequestMethod::PATCH])) {
            $_POST = $params;
            $_SERVER['CONTENT_TYPE'] = $normalizedHeaders['content-type'] ?? 'application/x-www-form-urlencoded';
        } else {
            $_GET = $params;
        }

        putenv('REQUEST_METHOD=' . $method);

        foreach ($normalizedHeaders as $name => $value) {
            if ($name !== 'content-type') {
                $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $name))] = $value;
            }
        }
    }
}
