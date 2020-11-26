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
 * A class that represents HTTP client request.
 * The developer can use this class to access basic information about a 
 * request. Note that it does not comply with PSR-7 in all aspects.
 * @author Ibrahim
 * 
 * @version 1.0
 */
class Request {
    /**
     * An array that contains the names of request methods.
     * 
     * This array contains the following strings:
     * <ul>
     * <li>GET</li>
     * <li>HEAD</li>
     * <li>POST</li>
     * <li>PUT</li>
     * <li>DELETE</li>
     * <li>TRACE</li>
     * <li>OPTIONS</li>
     * <li>PATCH</li>
     * <li>CONNECT</li>
     * </ul>
     * 
     * @var array An array that contains the names of request methods.
     * 
     * @since 1.0
     */
    const METHODS = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'DELETE',
        'TRACE',
        'OPTIONS',
        'PATCH',
        'CONNECT'
    ];
    private $method;
    /**
     *
     * @var string
     * 
     * @since 1.0 
     */
    private $requestedUri;
    private $servicesManager;
    /**
     *
     * @var array
     * 
     * @since 1.0 
     */
    private $requestHeaders;
    private function __construct() {
        $this->requestedUri = null;
        $this->requestHeaders = null;
        
    }
    /**
     * Returns request content type.
     * 
     * @return string|null The value of the header 'content-type' in the request.
     * 
     * @since 1.0
     */
    public static function getContentType() {
        $c = isset($_SERVER['CONTENT_TYPE']) ? filter_var($_SERVER['CONTENT_TYPE']) : null;

        if ($c !== null && $c !== false) {
            return trim(explode(';', $c)[0]);
        }

        return null;
    }
    /**
     * Returns the IP address of the user who is connected to the server.
     * 
     * @return string The IP address of the user who is connected to the server. 
     * The value is taken from the array $_SERVER at index 'REMOTE_ADDR'.
     * 
     * @since 1.0
     */
    public static function getClientIP() {
        $ip = filter_var($_SERVER['REMOTE_ADDR'],FILTER_VALIDATE_IP);

        if ($ip == '::1') {
            return '127.0.0.1';
        } else {
            return $ip;
        }
    }
    /**
     * 
     * @param WebServicesManager $new
     * 
     * @return WebServicesManager
     */
    public function servicesManager(WebServicesManager $new = null) {
        if ($new !== null) {
            self::get()->servicesManager = $new;
        }
        return self::get()->servicesManager;
    }
    /**
     * Returns an object that holds all information about requested URI.
     * 
     * @return Uri an object that holds all information about requested URI.
     * 
     * @since 1.0
     */
    public static function getUri() {
        return new Uri(self::getRequestedURL());
    }
    /**
     * Returns the name of request method which is used to call one of the services in the set.
     * 
     * @return string Request method such as POST, GET, etc.... Default return 
     * value is 'GET'.
     * 
     * @since 1.0
     */
    public static function getMethod() {
        $method = filter_var(getenv('REQUEST_METHOD'), FILTER_SANITIZE_STRING);

        if (!in_array($method, self::METHODS)) {
            $method = 'GET';
        }

        return $method;
    }
    /**
     *
     * @var Response
     * 
     * @since 1.0 
     */
    private static $inst;
    /**
     * 
     * @return Request
     * 
     * @since 1.0
     */
    private static function get() {
        if (self::$inst === null) {
            self::$inst = new Request();
        }
        return self::$inst;
    }
    /**
     * Returns the URI of the requested resource.
     * 
     * @return string The URI of the requested resource. 
     * 
     * @since 1.0
     */
    public static function getRequestedURL() {
        if (self::get()->requestedUri !== null) {
            return self::get()->requestedUri;
        }
        $base = Uri::getBaseURL();
        
        $requestedURI = trim(filter_var(getenv('REQUEST_URI')),'/');
        self::get()->requestedUri = $base.'/'.$requestedURI;
        
        return self::get()->requestedUri;
    }
    /**
     * Returns HTTP request headers.
     * 
     * This method will try to extract request headers using two ways, 
     * first, it will check if the method 'apache_request_headers()' is 
     * exist or not. If it does, then request headers will be taken from 
     * there. If it does not exist, it will try to extract request headers 
     * from the super global $_SERVER.
     * 
     * @return array An associative array of request headers. The indices 
     * will represents the headers and the values are the values of the 
     * headers. The indices will be all in lower case.
     * 
     * @since 1.0
     */
    public static function getRequestHeaders() {
        if (self::get()->requestHeaders !== null) {
            return self::get()->requestHeaders;
        }
        self::get()->requestHeaders = [];

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();

            foreach ($headers as $k => $v) {
                self::get()->requestHeaders[strtolower($k)] = filter_var($v, FILTER_SANITIZE_STRING);
            }
        } else if (isset($_SERVER)) {
            self::get()->requestHeaders = self::_getRequestHeadersFromServer();
        }

        return self::get()->requestHeaders;
    }
    /**
     * Collect request headers from the array $_SERVER.
     * @return array
     */
    private static function _getRequestHeadersFromServer() {
        $retVal = [];

        foreach ($_SERVER as $k => $v) {
            $split = explode('_', $k);

            if ($split[0] == 'HTTP') {
                $headerName = '';
                $count = count($split);

                for ($x = 0 ; $x < $count ; $x++) {
                    if ($x + 1 == $count && $split[$x] != 'HTTP') {
                        $headerName = $headerName.$split[$x];
                    } else if ($x == 1 && $split[$x] != 'HTTP') {
                        $headerName = $split[$x].'-';
                    } else if ($split[$x] != 'HTTP') {
                        $headerName = $headerName.$split[$x].'-';
                    }
                }
                $retVal[strtolower($headerName)] = filter_var($v, FILTER_SANITIZE_STRING);
            }
        }

        return $retVal;
    }
}
