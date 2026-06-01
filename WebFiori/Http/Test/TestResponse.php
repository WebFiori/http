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

use PHPUnit\Framework\Assert;

/**
 * Wraps a service response for fluent assertions in tests.
 *
 * @author Ibrahim
 */
class TestResponse {
    private string $body;
    private ?array $json;

    public function __construct(string $body) {
        $this->body = $body;
        $this->json = json_decode($body, true);
    }
    /**
     * Returns the raw response body.
     * 
     * @return string
     */
    public function getBody(): string {
        return $this->body;
    }
    /**
     * Returns the decoded JSON body, or null if not valid JSON.
     * 
     * @return array|null
     */
    public function getJson(): ?array {
        return $this->json;
    }
    /**
     * Returns the HTTP status code from the JSON response.
     * 
     * @return int
     */
    public function getStatusCode(): int {
        return $this->json['http-code'] ?? 200;
    }
    /**
     * Assert the response has a specific HTTP status code.
     * 
     * @return self
     */
    public function assertStatus(int $code): self {
        Assert::assertEquals($code, $this->getStatusCode(), "Expected status $code, got {$this->getStatusCode()}");
        return $this;
    }
    /**
     * Assert the response is successful (no error status).
     * 
     * @return self
     */
    public function assertOk(): self {
        Assert::assertFalse(
            isset($this->json['http-code']) && $this->json['http-code'] >= 400,
            "Expected successful response, got status {$this->getStatusCode()}"
        );
        return $this;
    }
    /**
     * Assert the response is 401 Unauthorized.
     * 
     * @return self
     */
    public function assertUnauthorized(): self {
        return $this->assertStatus(401);
    }
    /**
     * Assert the response is 404 Not Found.
     * 
     * @return self
     */
    public function assertNotFound(): self {
        return $this->assertStatus(404);
    }
    /**
     * Assert the response is 405 Method Not Allowed.
     * 
     * @return self
     */
    public function assertMethodNotAllowed(): self {
        return $this->assertStatus(405);
    }
    /**
     * Assert the response body is valid JSON.
     * 
     * @return self
     */
    public function assertJson(): self {
        Assert::assertNotNull($this->json, 'Response body is not valid JSON');
        return $this;
    }
    /**
     * Assert the JSON response contains a specific key.
     * 
     * @return self
     */
    public function assertJsonHas(string $key): self {
        Assert::assertNotNull($this->json, 'Response body is not valid JSON');
        Assert::assertArrayHasKey($key, $this->json, "JSON response missing key '$key'");
        return $this;
    }
    /**
     * Assert a JSON key equals an expected value.
     * 
     * @return self
     */
    public function assertJsonEquals(string $key, mixed $expected): self {
        Assert::assertNotNull($this->json, 'Response body is not valid JSON');
        Assert::assertArrayHasKey($key, $this->json, "JSON response missing key '$key'");
        Assert::assertEquals($expected, $this->json[$key], "JSON key '$key' does not match expected value");
        return $this;
    }
    /**
     * Assert the response body contains a substring.
     * 
     * @return self
     */
    public function assertBodyContains(string $substring): self {
        Assert::assertStringContainsString($substring, $this->body);
        return $this;
    }
    /**
     * Assert the response type is 'error'.
     * 
     * @return self
     */
    public function assertError(): self {
        return $this->assertJsonEquals('type', 'error');
    }
}
