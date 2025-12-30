<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2024-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * Base class for HTTP messages (Request and Response).
 *
 * @author Ibrahim
 */
class HttpMessage {
    /**
     * @var string
     */
    private $body;
    /**
     * @var HeadersPool
     */
    private $headersPool;

    /**
     * @var string
     */
    private $protocolVersion;

    /**
     * @var string
     */
    private $requestMethod;

    /**
     * Creates new instance of the class.
     */
    public function __construct() {
        $this->headersPool = new HeadersPool();
        $this->body = '';
        $this->protocolVersion = '1.1';
        $this->requestMethod = 'GET';
    }

    /**
     * Adds a header to the message.
     * 
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @param string|null $replaceValue Optional value to replace.
     * 
     * @return bool
     */
    public function addHeader(string $name, string $value, ?string $replaceValue = '') : bool {
        return $this->headersPool->addHeader($name, $value, $replaceValue);
    }

    /**
     * Gets the body of the message.
     * 
     * @return string
     */
    public function getBody() : string {
        return $this->body;
    }

    /**
     * Returns the value(s) of specific HTTP header.
     * 
     * @param string $name The name of the header.
     * 
     * @return array
     */
    public function getHeader(string $name) : array {
        return $this->headersPool->getHeader($name);
    }

    /**
     * Returns an array that contains all headers.
     * 
     * @return array
     */
    public function getHeaders() : array {
        return $this->headersPool->getHeaders();
    }

    /**
     * Returns the headers pool.
     * 
     * @return HeadersPool
     */
    public function getHeadersPool() : HeadersPool {
        return $this->headersPool;
    }

    /**
     * Gets the protocol version.
     * 
     * @return string
     */
    public function getProtocolVersion() : string {
        return $this->protocolVersion;
    }

    /**
     * Gets the request method.
     * 
     * @return string
     */
    public function getRequestMethod() : string {
        return $this->requestMethod;
    }

    /**
     * Checks if specific header exists.
     * 
     * @param string $name The name of the header.
     * @param string|null $val Optional header value to check.
     * 
     * @return bool
     */
    public function hasHeader(string $name, ?string $val = '') : bool {
        return $this->headersPool->hasHeader($name, $val);
    }

    /**
     * Removes specific header.
     * 
     * @param string $name The name of the header.
     * @param string|null $val Optional header value to remove.
     * 
     * @return bool
     */
    public function removeHeader(string $name, ?string $val = '') : bool {
        return $this->headersPool->removeHeader($name, $val);
    }

    /**
     * Sets the body of the message.
     * 
     * @param string $body
     */
    public function setBody(string $body) {
        $this->body = $body;
    }

    /**
     * Sets the protocol version.
     * 
     * @param string $version
     */
    public function setProtocolVersion(string $version) {
        $this->protocolVersion = $version;
    }

    /**
     * Sets the request method.
     * 
     * @param string $method
     */
    public function setRequestMethod(string $method) {
        $this->requestMethod = $method;
    }
}
