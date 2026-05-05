<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 * 
 */
namespace WebFiori\Http;

/**
 * A wrapper class that allows methods annotated with #[ResponseBody] to return
 * dynamic HTTP status codes and content types along with the response body.
 * 
 * @author Ibrahim
 */
class ResponseEntity {
    /**
     * Creates a new ResponseEntity instance.
     * 
     * @param mixed $body The response body content. Can be a Json object, array, string, or null.
     * 
     * @param int $status The HTTP status code to send with the response. Default is 200.
     * 
     * @param string $contentType The content type header value. Default is 'application/json'.
     */
    public function __construct(
        private mixed $body,
        private int $status = 200,
        private string $contentType = 'application/json'
    ) {
    }

    /**
     * Returns the response body.
     * 
     * @return mixed The body content of the response.
     */
    public function getBody(): mixed {
        return $this->body;
    }

    /**
     * Returns the HTTP status code.
     * 
     * @return int The HTTP status code.
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * Returns the content type of the response.
     * 
     * @return string The content type header value.
     */
    public function getContentType(): string {
        return $this->contentType;
    }

    /**
     * Creates a ResponseEntity with HTTP 200 OK status.
     * 
     * @param mixed $body The response body content.
     * 
     * @return self A new ResponseEntity instance with status 200.
     */
    public static function ok(mixed $body): self {
        return new self($body, 200);
    }

    /**
     * Creates a ResponseEntity with HTTP 201 Created status.
     * 
     * @param mixed $body The response body content.
     * 
     * @return self A new ResponseEntity instance with status 201.
     */
    public static function created(mixed $body): self {
        return new self($body, 201);
    }

    /**
     * Creates a ResponseEntity with HTTP 204 No Content status and null body.
     * 
     * @return self A new ResponseEntity instance with status 204 and no body.
     */
    public static function noContent(): self {
        return new self(null, 204);
    }

    /**
     * Creates a ResponseEntity with HTTP 400 Bad Request status.
     * 
     * @param mixed $body The response body content describing the error.
     * 
     * @return self A new ResponseEntity instance with status 400.
     */
    public static function badRequest(mixed $body): self {
        return new self($body, 400);
    }

    /**
     * Creates a ResponseEntity with HTTP 401 Unauthorized status.
     * 
     * @param mixed $body The response body content describing the authentication failure.
     * 
     * @return self A new ResponseEntity instance with status 401.
     */
    public static function unauthorized(mixed $body): self {
        return new self($body, 401);
    }

    /**
     * Creates a ResponseEntity with HTTP 403 Forbidden status.
     * 
     * @param mixed $body The response body content describing the authorization failure.
     * 
     * @return self A new ResponseEntity instance with status 403.
     */
    public static function forbidden(mixed $body): self {
        return new self($body, 403);
    }

    /**
     * Creates a ResponseEntity with HTTP 404 Not Found status.
     * 
     * @param mixed $body The response body content describing what was not found.
     * 
     * @return self A new ResponseEntity instance with status 404.
     */
    public static function notFound(mixed $body): self {
        return new self($body, 404);
    }

    /**
     * Creates a ResponseEntity with HTTP 500 Internal Server Error status.
     * 
     * @param mixed $body The response body content describing the error.
     * 
     * @return self A new ResponseEntity instance with status 500.
     */
    public static function error(mixed $body): self {
        return new self($body, 500);
    }
}
