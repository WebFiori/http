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
     * @var array
     * 
     * @since 1.0 
     */
    private $headers;
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
        $this->headers = [];
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
     * @param boolean $isReplace If the header is already exist and this parameter 
     * is set to true, the method will override all existing header values with 
     * the given value.
     * 
     * @return boolean If the header is added, the method will return true. If 
     * not added, the method will return false.
     * 
     * @since 1.0
     */
    public static function addHeader($headerName, $headerVal, $isReplace = false) {
        $trimmedHeader = strtolower(trim($headerName));
        $replace = $isReplace === true;
        $retVal = false;

        if (self::_validateheaderName($trimmedHeader) && strlen($headerVal) != 0) {
            $hasHeader = self::hasHeader($trimmedHeader);

            if ($hasHeader && $replace) {
                self::get()->headers[$trimmedHeader] = $headerVal;
                $retVal = true;
            } else if (!$hasHeader) {
                self::get()->headers[$trimmedHeader] = [$headerVal];
                $retVal = true;
            } else if (!$replace) {
                self::get()->headers[$trimmedHeader][] = $headerVal;
                $retVal = true;
            }
        }

        return $retVal;
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
        self::get()->headers = [];

        return self::get();
    }
    /**
     * Returns a string that represents response body that will be send.
     * 
     * @return string A string that represents response body that will be send.
     * 
     * @since 1.0
     */
    public static function getBody() {
        return self::get()->body;
    }
    /**
     * Returns the value of HTTP response code that will be sent.
     * 
     * @return int HTTP response code. Default value is 200.
     * 
     * @since 1.0
     */
    public static function getCode() {
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
    public static function getHeader($headerName) {
        $trimmed = strtolower(trim($headerName));

        if (isset(self::get()->headers[$trimmed])) {
            return self::get()->headers[$trimmed];
        }

        return [];
    }
    /**
     * Returns an associative array that contains response headers.
     * 
     * The returned array will only contain information about the headers which are 
     * added using the method Response::addHeader().
     * 
     * @return array An associative array. The indices will be headers names and 
     * the value of each index will be sub array that contains header values.
     * 
     * @since 1.0
     */
    public static function getHeaders() {
        return self::get()->headers;
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
    public static function hasHeader($headerName, $headerVal = null) {
        $headerValFromObj = self::getHeader($headerName);

        if ($headerVal !== null) {
            foreach ($headerValFromObj as $val) {
                if ($val == $headerVal) {
                    return true;
                }
            }

            return false;
        }

        return count($headerValFromObj) != 0;
    }
    /**
     * Checks if the response was sent or not.
     * 
     * @return boolean The method will return true if output is sent. False 
     * if not.
     * 
     * @since 1.0.1
     */
    public static function isSent() {
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
    public static function removeHeader($headerName, $headerVal = null) {
        $trimmedName = strtolower(trim($headerName));
        $retVal = false;

        if (self::hasHeader($trimmedName)) {
            if ($headerVal !== null) {
                $values = self::getHeader($trimmedName);
                $count = count($values);

                if ($count == 1) {
                    unset(self::get()->headers[$trimmedName]);
                    $retVal = true;
                } else {
                    $newValsArr = [];

                    for ($x = 0 ; $x < $count ; $x++) {
                        if ($values[$x] != $headerVal) {
                            $newValsArr[] = $values[$x];
                        } else {
                            $retVal = true;
                        }
                    }
                    self::get()->headers[$trimmedName] = $newValsArr;
                }
            } else {
                unset(self::get()->headers[$trimmedName]);
                $retVal = true;
            }
        }

        return $retVal;
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
    public static function setCode($code) {
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
    public static function write($str) {
        self::get()->body .= $str;

        return self::get();
    }
    private static function _validateheaderName($name) {
        $len = strlen($name);

        if ($len == 0) {
            return false;
        }

        for ($x = 0 ; $x < $len ; $x++) {
            $char = $name[$x];

            if (!(($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || $char == '_' || $char == '-')) {
                return false;
            }
        }

        return true;
    }
    /**
     * 
     * @return Response
     */
    private static function get() {
        if (self::$inst === null) {
            self::$inst = new Response();
        }

        return self::$inst;
    }
}
