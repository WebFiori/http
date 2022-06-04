<?php
/**
 * MIT License
 *
 * Copyright (c) 2019, WebFiori HTTP.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace webfiori\http;


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
 * @version 1.0.1
 * 
 */
class Response {
    /**
     *
     * @var boolean
     * 
     * @since 1.0.1 
     */
    private $isSent;
    /**
     *
     * @var array 
     * 
     * @since 1.0
     */
    private $beforeSendCalls;
    /**
     *
     * @var string
     * 
     * @since 1.0 
     */
    private $body;
    /**
     *
     * @var HeadersPool
     * 
     * @since 1.0 
     */
    private $headersPool;
    /**
     *
     * @var Response
     * 
     * @since 1.0 
     */
    private static $inst;
    /**
     *
     * @var boolean
     * 
     * @since 1.0 
     */
    private $lock;
    /**
     *
     * @var int
     * 
     * @since 1.0 
     */
    private $responseCode;
    /**
     * @since 1.0
     */
    private function __construct() {
        $this->headersPool = new HeadersPool();
        $this->body = '';
        $this->responseCode = 200;
        $this->lock = false;
        $this->isSent = false;
        $this->beforeSendCalls = [];
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
     * @return boolean If the header is added, the method will return true. If 
     * not added, the method will return false.
     * 
     * @since 1.0
     */
    public static function addHeader(string $headerName, string $headerVal, string $replaceValue = null) {
        return self::getHeadersPool()->addHeader($headerName, $headerVal, $replaceValue);
    }
    /**
     * Adds a function to execute before sending the final response.
     * 
     * This method can be used to add more than one callback. 
     * 
     * @param Closure $func A PHP callable.
     * 
     * @since 1.0
     */
    public static function beforeSend($func) {
        if (is_callable($func)) {
            self::get()->beforeSendCalls[] = $func;
        }
    }
    /**
     * Removes all added headers and reset the body of the response.
     * 
     * @return Response 
     * 
     * @since 1.0
     */
    public static function clear() {
        self::clearBody()->clearHeaders();

        return self::get();
    }
    /**
     * Reset the body of the response.
     * 
     * @return Response 
     * 
     * @since 1.0
     */
    public static function clearBody() {
        self::get()->body = '';

        return self::get();
    }
    /**
     * Removes all headers which where added to the response.
     * 
     * @return Response 
     * 
     * @since 1.0
     */
    public static function clearHeaders() {
        self::get()->headersPool = new HeadersPool();

        return self::get();
    }
    /**
     * Returns a string that represents response body that will be send.
     * 
     * @return string A string that represents response body that will be send.
     * 
     * @since 1.0
     */
    public static function getBody() : string {
        return self::get()->body;
    }
    /**
     * Returns the value of HTTP response code that will be sent.
     * 
     * @return int HTTP response code. Default value is 200.
     * 
     * @since 1.0
     */
    public static function getCode() : int {
        return self::get()->responseCode;
    }
    /**
     * Returns the value(s) of specific HTTP header.
     * 
     * @param array $headerName The name of the header.
     * 
     * @return array If such header exist, the method will return an array 
     * that contains the values of the header. If the header does not exist, the 
     * method will return an empty array.
     * 
     * @since 1.0
     */
    public static function getHeader(string $headerName) {
        return self::getHeadersPool()->getHeader($headerName);
    }
    /**
     * Returns an array that contains response headers as object.
     * 
     * @return array An array that contains response headers as object.
     * 
     * @since 1.0
     */
    public static function getHeaders() : array {
        return self::getHeadersPool()->getHeaders();
    }
    public static function getHeadersPool() : HeadersPool {
        return self::get()->headersPool;
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
     * @return boolean If a header which has the given name exist, the method 
     * will return true. If a value is specified and a match is fond, the 
     * method will return true. Other than that, the method will return true.
     * 
     * @since 1.0 
     */
    public static function hasHeader(string $headerName, string $headerVal = null) {
        return self::getHeadersPool()->hasHeader($headerName, $headerVal);
    }
    /**
     * Checks if the response was sent or not.
     * 
     * @return boolean The method will return true if output is sent. False 
     * if not.
     * 
     * @since 1.0.1
     */
    public static function isSent() : bool {
        return self::get()->isSent;
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
     * @return boolean If the header is removed, the method will return true. Other 
     * than that, the method will return true.
     * 
     * @since 1.0
     */
    public static function removeHeader(string $headerName, $headerVal = null) {
        return self::getHeadersPool()->removeHeader($headerName, $headerVal);
    }
    /**
     * Send the response.
     * 
     * Note that if this method is called outside CLI environment,
     * it will terminate the execution of code once the output is sent. In terminal 
     * environment, calling it will have no effect.
     * 
     * @since 1.0
     */
    public static function send() {
        if (!self::isSent()) {
            if (!self::get()->lock) {
                self::get()->lock = true;
                foreach (self::get()->beforeSendCalls as $func) {
                    call_user_func($func);
                }
            }

            if (!(http_response_code() === false)) {
                self::get()->isSent = true;
                // Send response only in non-cli environment.

                http_response_code(self::getCode());

                foreach (self::getHeaders() as $headerName => $headerVals) {
                    foreach ($headerVals as $headerVal) {
                        header($headerName.': '.$headerVal, false);
                    }
                }

                if (is_callable('fastcgi_finish_request')) {
                    echo self::getBody();
                    fastcgi_finish_request();
                } else {
                    ob_start();
                    echo self::getBody();
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
     * @since 1.0
     */
    public static function setCode(int $code) {
        $asInt = intval($code);

        if ($asInt >= 100 && $asInt <= 599) {
            self::get()->responseCode = $code;
        }
    }
    /**
     * Appends a string to response body.
     * 
     * @param string $str The string that will be appended.
     * 
     * @return Response 
     * 
     * @since 1.0
     */
    public static function write(string $str) {
        self::get()->body .= $str;

        return self::get();
    }
    /**
     * Returns an instance of the class.
     * 
     * @return Response
     */
    public static function get() {
        if (self::$inst === null) {
            self::$inst = new Response();
        }

        return self::$inst;
    }
}
