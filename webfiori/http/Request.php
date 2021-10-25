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
 * @version 1.0.1
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
    /**
     *
     * @var Response
     * 
     * @since 1.0 
     */
    private static $inst;
    /**
     *
     * @var string
     * 
     * @since 1.0 
     */
    private $requestedUri;
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
     * Returns the value of a GET or POST parameter.
     * 
     * This method will apply basic filtering to the value of the parameter before returning 
     * it. The developer may need to apply extra filtering to make sure that the 
     * value of the parameter is safe to use.
     * 
     * @param string $paramName The name of the parameter. Note that if the value has extra 
     * spaces, they will be trimmed.
     * 
     * @return string|null The method will return the value of the parameter if 
     * set as a string. Other than that, the method will return null.
     * 
     * @since 1.0.1
     */
    public static function getParam($paramName) {
        $requMethod = self::getMethod();
        $trimmed = trim($paramName);
        $val = null;
        
        if ($requMethod == 'POST' || $requMethod == 'PUT') {
            $val = filter_input(INPUT_POST, $trimmed);
        } else if ($requMethod == 'DELETE' || $requMethod == 'GET') {
            $val = filter_input(INPUT_GET, $trimmed);
        }
        
        if ($val === false) {
            return null;
        }
        return $val;
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
     * Returns the name of request method which is used to call one of the services in the set.
     * 
     * @return string Request method such as POST, GET, etc.... Default return 
     * value is 'GET'. The default is usually returned in case the call to 
     * this method was performed in CLI environment. To change request method 
     * in CLI environment to something like 'POST' for testing, use the 
     * function putenv('REQUEST_METHOD=POST'). 
     * 
     * @since 1.0
     */
    public static function getMethod() {
        $meth = getenv('REQUEST_METHOD');
        
        if ($meth === false) {
            $meth = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        }
        $method = filter_var($meth, FILTER_SANITIZE_STRING);
        
        
        if (!in_array($method, self::METHODS)) {
            $method = 'GET';
        }

        return $method;
    }
    /**
     * Returns the URI of the requested resource.
     * 
     * @return string The URI of the requested resource. 
     * 
     * @since 1.0
     */
    public static function getRequestedURL() {
        if (self::get()->requestedUri === null) {
            $base = Uri::getBaseURL();
            $path = getenv('REQUEST_URI');

            if ($path === false) {
                // Using built-in server, it will be false
                $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
                
            } 
            $toAppend = trim(filter_var($path),'/');
            
            if (defined('WF_PATH_TO_APPEND')) {
                $toAppend = str_replace(trim(str_replace('\\', '/', WF_PATH_TO_APPEND), '/'),'' ,$toAppend);
            }
            
            self::get()->requestedUri = $base.'/'.trim($toAppend, '/');
        }


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
    public static function getHeaders() {
        if (self::get()->requestHeaders === null || defined('__PHPUNIT_PHAR__')) {
            //Refresh headers if in testing environment.
            self::get()->requestHeaders = [];

            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();

                foreach ($headers as $k => $v) {
                    self::get()->requestHeaders[strtolower($k)] = filter_var($v, FILTER_SANITIZE_STRING);
                }
            } 
            
            if (isset($_SERVER)) {
                self::get()->requestHeaders = array_merge(self::get()->requestHeaders, self::_getRequestHeadersFromServer());
            }
        }


        return self::get()->requestHeaders;
    }
    /**
     * Returns an array that contains the value of the header 'authorization'.
     * 
     * 
     * @return array The array will have two indices, the first one with 
     * name 'scheme' and the second one with name 'credentials'. The index 'scheme' 
     * will contain the name of the scheme which is used to authenticate 
     * ('Basic', 'Bearer', 'Digest', etc...). The index 'credentials' will contain 
     * the credentials which can be used to authenticate the client.
     * 
     *  @since 1.0
     */
    public static function getAuthHeader() {
        $retVal = [
            'scheme' => '',
            'credentials' => ''
        ];
        $headerVal = '';
        
        $headers = self::getHeaders();
        
        if (isset($headers['authorization'])) {
            $headerVal = $headers['authorization'];
        }

        if (strlen($headerVal) != 0) {
            $split = explode(' ', $headerVal);

            if (count($split) == 2) {
                $retVal['scheme'] = strtolower($split[0]);
                $retVal['credentials'] = $split[1];
            }
        }

        return $retVal;
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
}
