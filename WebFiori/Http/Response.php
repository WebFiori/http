<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * A class that represents HTTP response.
 * 
 * This class can be used to collect server output and send it back to the client.
 * In addition, it can be used to send custom headers to the client. This class 
 * is used to solve the error 'Output already started at XXX'. Note that 
 * this class does not comply with PSR-7 specifications.
 *
 * @author Ibrahim
 * 
 * 
 */
class Response {
    private array $beforeSendCalls;
    private string $body;
    private array $cookies;
    private HeadersPool $headersPool;
    private bool $isSent;
    private bool $lock;
    private int $responseCode;

    public function __construct() {
        $this->headersPool = new HeadersPool();
        $this->body = '';
        $this->responseCode = 200;
        $this->lock = false;
        $this->isSent = false;
        $this->beforeSendCalls = [];
        $this->cookies = [];
    }

    /**
     * Adds new cookie to the list of response cookies.
     * 
     * @param HttpCookie $cookie An object that holds cookie properties.
     */
    public function addCookie(HttpCookie $cookie) {
        $this->cookies[] = $cookie;
    }

    /**
     * Adds new HTTP header to the response.
     * 
     * @param string $headerName The name of the header.
     * 
     * @param string $headerVal The value of the header.
     * 
     * @param string $replaceValue If the header is already exist and this parameter 
     * is specified, the method will override existing header with the specified
     * value with the given new value. Note that if no header was found which
     * has the given value, the header will be added as new one.
     * 
     * @return bool If the header is added, the method will return true. If 
     * not added, the method will return false.
     * 
     */
    public function addHeader(string $headerName, string $headerVal, ?string $replaceValue = '') : bool {
        return $this->headersPool->addHeader($headerName, $headerVal, $replaceValue);
    }

    /**
     * Adds a function to execute before sending the final response.
     * 
     * This method can be used to add more than one callback. 
     * 
     * @param callable $func A PHP callable.
     * 
     */
    public function beforeSend(callable $func) {
        $this->beforeSendCalls[] = $func;
    }

    /**
     * Removes all added headers and reset the body of the response.
     * 
     * @return Response 
     * 
     */
    public function clear() : Response {
        $this->clearBody();
        $this->clearHeaders();

        return $this;
    }

    /**
     * Reset the body of the response.
     * 
     * @return Response 
     * 
     */
    public function clearBody() : Response {
        $this->body = '';

        return $this;
    }

    /**
     * Removes all headers which where added to the response.
     * 
     * @return Response 
     * 
     */
    public function clearHeaders() : Response {
        $this->headersPool = new HeadersPool();

        return $this;
    }

    /**
     * Display dump information of a variable.
     *
     * This method uses PHP's 'var_dump' to show information.
     *
     * @param mixed $value The value that its dump will be displayed.
     *
     * @param bool $send If this parameter is set to true, the response
     * will be sent and execution will be terminated.
     *
     * @return Response
     */
    public function dump($value, bool $send = true): Response {
        ob_start();
        var_dump($value);
        $this->body .= '<pre>'.ob_get_clean().'</pre>';

        if ($send) {
            $this->send();
        }

        return $this;
    }

    /**
     * Returns a string that represents response body that will be sent.
     * 
     * @return string A string that represents response body that will be sent.
     * 
     */
    public function getBody() : string {
        return $this->body;
    }

    /**
     * Returns the value of HTTP response code that will be sent.
     * 
     * @return int HTTP response code. Default value is 200.
     * 
     */
    public function getCode() : int {
        return $this->responseCode;
    }

    /**
     * Returns an object that holds cookie information given its name.
     * 
     * @param string $cookieName The name of the cookie.
     * 
     * @return HttpCookie|null If a cookie which has the given name exist,
     * the method will return it as an object. Other than that, null
     * is returned.
     */
    public function getCookie(string $cookieName) {
        foreach ($this->cookies as $cookie) {
            if ($cookie->getName() == $cookieName) {
                return $cookie;
            }
        }

        return null;
    }

    /**
     * Returns an array of all cookies that will be sent with the response.
     * 
     * @return array An array that holds objects of type 'HttpCookie'.
     */
    public function getCookies() : array {
        return $this->cookies;
    }

    /**
     * Returns the value(s) of specific HTTP header.
     * 
     * @param string $headerName The name of the header.
     * 
     * @return array If such header exist, the method will return an array 
     * that contains the values of the header. If the header does not exist, the 
     * method will return an empty array.
     * 
     */
    public function getHeader(string $headerName) : array {
        return $this->headersPool->getHeader($headerName);
    }

    /**
     * Returns an array that contains response headers as object.
     * 
     * @return array An array that contains response headers as object.
     * 
     */
    public function getHeaders() : array {
        return $this->headersPool->getHeaders();
    }

    /**
     * Returns the instance which is used to hold all http headers that
     * will be sent with the request.
     * 
     * @return HeadersPool
     */
    public function getHeadersPool() : HeadersPool {
        return $this->headersPool;
    }

    /**
     * Checks if the response will have specific cookie given its name.
     * 
     * @param string $cookieName The name of the cookie.
     * 
     * @return bool If the response have a cookie with specified name,
     * the method will return true. False if not.
     */
    public function hasCookie(string $cookieName) : bool {
        return $this->getCookie($cookieName) !== null;
    }

    /**
     * Checks if the response will have specific header or not.
     * 
     * This method will only check for headers which are added using the method 
     * Response::addHeader().
     * 
     * @param string $headerName The name of the header (such as 'content-type'). 
     * 
     * @param string $headerVal An optional value to check for. Default is null 
     * which means only check for the name.
     * 
     * @return bool If a header which has the given name exist, the method 
     * will return true. If a value is specified and a match is fond, the 
     * method will return true. Other than that, the method will return true.
     * 
     */
    public function hasHeader(string $headerName, ?string $headerVal = '') : bool {
        return $this->headersPool->hasHeader($headerName, $headerVal);
    }

    /**
     * Checks if the response was sent or not.
     * 
     * @return bool The method will return true if output is sent. False 
     * if not.
     * 
     */
    public function isSent() : bool {
        return $this->isSent;
    }

    /**
     * Removes a header from the response.
     * 
     * @param string $headerName The name of the header.
     * 
     * @param string|null $headerVal An optional header value. If the header has 
     * multiple values and this one is specified, only the given header value 
     * will be removed.
     * 
     * @return bool If the header is removed, the method will return true.
     * Other than that, the method will return true.
     * 
     */
    public function removeHeader(string $headerName, ?string $headerVal = '') : bool {
        return $this->headersPool->removeHeader($headerName, $headerVal);
    }

    /**
     * Send the response.
     * 
     * Note that if this method is called outside CLI environment,
     * it will terminate the execution of code once the output is sent. In terminal 
     * environment, calling it will have no effect.
     * 
     */
    public function send() {
        if (!$this->isSent) {
            if (!$this->lock) {
                $this->lock = true;

                foreach ($this->beforeSendCalls as $func) {
                    call_user_func($func);
                }
            }

            if (!(http_response_code() === false)) {
                $this->isSent = true;

                http_response_code($this->responseCode);

                foreach ($this->getHeaders() as $headerObj) {
                    header($headerObj.'', false);
                }

                foreach ($this->getCookies() as $cookie) {
                    header($cookie->getHeader().'', false);
                }

                if (is_callable('fastcgi_finish_request')) {
                    echo $this->body;
                    fastcgi_finish_request();
                } else {
                    ob_start();
                    echo $this->body;
                    ob_end_flush();

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
                die;
            }
        }
    }

    /**
     * Sets the value of HTTP response code that will be sent.
     * 
     * @param int $code HTTP response code. The value must be between 100 and 
     * 599 inclusive.
     * 
     */
    public function setCode(int $code) {
        if ($code >= 100 && $code <= 599) {
            $this->responseCode = $code;
        }
    }

    /**
     * Appends a value to response body.
     *
     * @param mixed $value The value that will be appended.
     *
     * @param bool $sendResponse If this parameter is set to true, the response
     * will be sent and execution will be terminated.
     *
     * @return Response
     *
     */
    public function write($value, bool $sendResponse = false) : Response {
        $type = gettype($value);
        $dumpTypes = [
            'resource (closed)',
            'resource',
            'boolean',
            'array',
            'unknown type',
            'NULL'
        ];

        if (($type == 'object' && !method_exists($value, '__toString')) || in_array($type, $dumpTypes)) {
            ob_start();
            var_dump($value);
            $this->body .= '<pre>'.ob_get_clean().'</pre>';
        } else {
            $this->body .= $value;
        }

        if ($sendResponse) {
            $this->send();
        }

        return $this;
    }
}
