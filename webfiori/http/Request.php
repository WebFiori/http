<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
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

    private function __construct() {
        $this->headersPool = new HeadersPool();
    }
    /**
     * Returns an instance of the class.
     * 
     * @return Request
     * 
     * @since 1.0
     */
    public static function get() {
        if (self::$inst === null) {
            self::$inst = new Request();
        }

        return self::$inst;
    }
    /**
     * Returns an array that contains the value of the header 'authorization'.
     * 
     * @return array The array will have two indices, the first one with 
     * name 'scheme' and the second one with name 'credentials'. The index 'scheme' 
     * will contain the name of the scheme which is used to authenticate 
     * ('Basic', 'Bearer', 'Digest', etc...). The index 'credentials' will contain 
     * the credentials which can be used to authenticate the client.
     * 
     *  @since 1.0
     */
    public static function getAuthHeader() : array {
        $retVal = [
            'scheme' => '',
            'credentials' => ''
        ];
        $headerVal = '';

        $header = self::getHeader('authorization');

        if (count($header) == 1) {
            $headerVal = $header[0];
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
     * Returns the IP address of the user who is connected to the server.
     * 
     * @return string The IP address of the user who is connected to the server. 
     * The value is taken from the array $_SERVER at index 'REMOTE_ADDR'.
     * If the IP address is invalid, empty string is returned.
     * 
     * @since 1.0
     */
    public static function getClientIP() : string {
        if (!isset($_SERVER['REMOTE_ADDR'])) {
            return '127.0.0.1';
        }
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
     * Returns HTTP header given its name.
     * 
     * @param string $name The name of the header.
     * 
     * @return array If a header which has the given name exist,
     * the method will return all header values as an array. If the header
     * does not exist, the array will be empty.
     */
    public static function getHeader(string $name) : array {
        self::getHeaders();

        return self::getHeadersPool()->getHeader($name);
    }

    /**
     * Returns HTTP request headers.
     * 
     * This method will try to extract request headers using two ways, 
     * first, it will check if the method 'apache_request_headers()' is existed or not. If it does, then request headers will be taken from
     * there. If it does not exist, it will try to extract request headers 
     * from the super global $_SERVER.
     * 
     * @return array An array of request headers. Each header is represented
     * as an object of type HttpHeader.
     * 
     * @since 1.0
     */
    public static function getHeaders() : array {
        if (defined('__PHPUNIT_PHAR__') || self::get()->headersPool === null) {
            //Always Refresh headers if in testing environment.
            self::extractHeaders();
        }

        return self::getHeadersPool()->getHeaders();
    }
    /**
     * Returns an associative array of request headers.
     * 
     * @return array The indices of the array will be headers names and the
     * values are sub-arrays. Each array contains the values of the header.
     */
    public static function getHeadersAssoc() : array {
        $retVal = [];
        $headers = self::getHeaders();

        foreach ($headers as $headerObj) {
            if (!isset($retVal[$headerObj->getName()])) {
                $retVal[$headerObj->getName()] = [];
            }
            $retVal[$headerObj->getName()][] = $headerObj->getValue();
        }

        return $retVal;
    }

    /**
     * Returns the pool which is used to hold request headers.
     * 
     * @return HeadersPool
     */
    public static function getHeadersPool() : HeadersPool {
        return self::get()->headersPool;
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
    public static function getMethod() : string {
        $meth = getenv('REQUEST_METHOD');

        if ($meth === false) {
            $meth = $_SERVER['REQUEST_METHOD'] ?? '';
        }
        $method = filter_var($meth, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


        if (!in_array($method, self::METHODS)) {
            $method = 'GET';
        }

        return $method;
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
    public static function getParam(string $paramName) {
        $trimmed = trim($paramName);
        $params = self::getParams();

        if (isset($params[$trimmed])) {
            return $params[$trimmed];
        }

        return null;
    }
    /**
     * Returns an array that contains all POST or GET parameters.
     * 
     * @return array An array that contains all POST or GET parameters. The
     * indices of the array are parameters names and the value of each index
     * is the value of the parameter.
     */
    public static function getParams() : array {
        $requestMethod = self::getMethod();
        $retVal = [];

        if ($requestMethod == 'POST' || $requestMethod == 'PUT') {
            foreach (array_keys($_POST) as $name) {
                $retVal[$name] = self::filter(INPUT_POST, $name);
            }
        } else if ($requestMethod == 'DELETE' || $requestMethod == 'GET') {
            foreach (array_keys($_GET) as $name) {
                $retVal[$name] = self::filter(INPUT_GET, $name);
            }
        }

        return $retVal;
    }
    /**
     * Returns the URI of the requested resource.
     * 
     * @param string $pathToAppend If provided, this part will be 
     * appended to the final URI. It is useful in case of having multiple
     * applications on same domain to have different paths.
     * 
     * @return string The URI of the requested resource 
     * (e.g. http://example.com/get-random?range=[1,100]). 
     * 
     * @since 1.0
     */
    public static function getRequestedURI(string $pathToAppend = '') : string {
        $base = Uri::getBaseURL();
        $path = getenv('REQUEST_URI');

        if ($path === false) {
            // Using built-in server, it will be false
            $path = $_SERVER['PATH_INFO'] ?? '';
        } 

        $cleanedPath = str_replace(trim(str_replace('\\', '/', $pathToAppend), '/'),'' ,trim(filter_var($path),'/'));

        return $base.'/'.trim($cleanedPath, '/');
    }
    /**
     * Returns an object that holds all information about requested URI.
     * 
     * @return Uri an object that holds all information about requested URI.
     * 
     * @since 1.0
     */
    public static function getUri() : Uri {
        return new Uri(self::getRequestedURI());
    }
    private static function extractHeaders() {
        self::get()->headersPool = new HeadersPool();

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();

            foreach ($headers as $k => $v) {
                self::get()->headersPool->addHeader($k, filter_var($v, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            }
        } 

        if (isset($_SERVER)) {
            $headersArr = self::getRequestHeadersFromServer();

            foreach ($headersArr as $header) {
                self::get()->headersPool->addHeader($header->getName(), $header->getValue());
            }
        }
    }
    private  static function filter($inputSource, $varName) {
        $val = filter_input($inputSource, $varName);

        if ($val === null) {
            if ($inputSource == INPUT_POST && isset($_POST[$varName])) {
                $val = filter_var(urldecode($_POST[$varName]));
            } else if ($inputSource == INPUT_GET && isset($_GET[$varName])) {
                $val = filter_var(urldecode($_GET[$varName]));
            }
        }

        return $val;
    }
    /**
     * Collect request headers from the array $_SERVER.
     * @return array
     */
    private static function getRequestHeadersFromServer() : array {
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
                $retVal[] = new HttpHeader($headerName, filter_var($v, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            }
        }

        return $retVal;
    }
}
