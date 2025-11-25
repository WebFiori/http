<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2024 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use InvalidArgumentException;

/**
 * Modern HTTP request class that extends HttpMessage.
 * 
 * @author Ibrahim
 */
class RequestV2 extends HttpMessage {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Creates a RequestV2 instance from global variables.
     * 
     * @return RequestV2
     */
    public static function createFromGlobals() : RequestV2 {
        $request = new RequestV2();
        $request->extractHeaders();
        $request->setRequestMethod($request->getMethodFromGlobals());
        $request->setBody(file_get_contents('php://input'));
        
        return $request;
    }
    /**
     * Returns an associative array of request headers.
     * 
     * @return array The indices of the array will be headers names and the
     * values are sub-arrays. Each array contains the values of the header.
     */
    public function getHeadersAssoc() : array {
        $retVal = [];
        $headers = $this->getHeaders();

        foreach ($headers as $headerObj) {
            if (!isset($retVal[$headerObj->getName()])) {
                $retVal[$headerObj->getName()] = [];
            }
            $retVal[$headerObj->getName()][] = $headerObj->getValue();
        }

        return $retVal;
    }
    /**
     * Returns authorization header.
     * 
     * @return AuthHeader|null
     */
    public function getAuthHeader() {
        $header = $this->getHeader('authorization');
        
        if (count($header) == 1) {
            return new AuthHeader($header[0]);
        }
        
        return null;
    }

    /**
     * Returns the IP address of the client.
     * 
     * @return string
     */
    public function getClientIP() : string {
        if (!isset($_SERVER['REMOTE_ADDR'])) {
            return '127.0.0.1';
        }
        
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if ($ip === '::1') {
            return '127.0.0.1';
        }
        
        $validated = filter_var($ip, FILTER_VALIDATE_IP);
        
        return $validated !== false ? $validated : '';
    }

    /**
     * Returns the content type header value.
     * 
     * @return string|null
     */
    public function getContentType() {
        return isset($_SERVER['CONTENT_TYPE']) ? filter_var($_SERVER['CONTENT_TYPE']) : null;
    }

    /**
     * Returns HTTP request method.
     * 
     * @return string
     */
    public function getMethod() : string {
        return $this->getRequestMethod();
    }

    /**
     * Returns the value of GET or POST parameter.
     * 
     * @param string $paramName
     * 
     * @return string|null
     */
    public function getParam(string $paramName) {
        $params = $this->getParams();
        return $params[trim($paramName)] ?? null;
    }

    /**
     * Returns the value of a cookie.
     * 
     * @param string $cookieName
     * 
     * @return string|null
     */
    public function getCookieValue(string $cookieName) {
        $trimmedName = trim($cookieName);
        
        if (isset($_COOKIE[$trimmedName])) {
            return filter_var($_COOKIE[$trimmedName]);
        }
        
        return null;
    }

    /**
     * Returns all request parameters.
     * 
     * @return array
     */
    public function getParams() : array {
        $method = $this->getMethod();
        $retVal = [];
        
        if ($method == RequestMethod::GET) {
            foreach ($_GET as $param => $val) {
                $retVal[$param] = $this->filter(INPUT_GET, $param);
            }
        } else if ($method == RequestMethod::POST) {
            foreach ($_POST as $param => $val) {
                $retVal[$param] = $this->filter(INPUT_POST, $param);
            }
        }
        
        return $retVal;
    }

    /**
     * Returns the path part of request URI.
     * 
     * @return string
     */
    public function getPath() : string {
        $path = $this->getPathHelper('REQUEST_URI');
        
        if ($path === null) {
            $path = $this->getPathHelper('PATH_INFO');
        }
        
        if ($path === null) {
            $path = $this->getPathHelper('HTTP_REQUEST_URI');
        }
        
        if ($path === null) {
            $path = $this->getPathHelper('HTTP_X_ORIGINAL_URL');
        }
        
        if ($path === null) {
            $path = $this->getPathHelper('SCRIPT_NAME');
        }
        
        if ($path === null) {
            return '/';
        }
        
        return parse_url($path, PHP_URL_PATH);
    }

    /**
     * Returns the URI of the requested resource.
     * 
     * @param string $pathToAppend
     * 
     * @return string
     */
    public function getRequestedURI(string $pathToAppend = '') : string {
        $base = RequestUri::getBaseURL();
        
        if (strlen($pathToAppend) != 0) {
            $path = $this->getPath();
            $cleanPath = trim($pathToAppend, '/');
            
            if (strlen($cleanPath) != 0) {
                if ($path[strlen($path) - 1] == '/') {
                    return $base.$path.$cleanPath;
                } else {
                    return $base.$path.'/'.$cleanPath;
                }
            }
        }
        
        $uri = $base.$this->getPath();
        
        if (!empty($_GET)) {
            $uri .= '?'.http_build_query($_GET);
        }
        
        return $uri;
    }

    /**
     * Returns an object of type 'Uri'.
     * 
     * @return RequestUri
     */
    public function getUri() : RequestUri {
        return new RequestUri($this->getRequestedURI());
    }

    private function getMethodFromGlobals() : string {
        $meth = getenv('REQUEST_METHOD');

        if ($meth === false) {
            $meth = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : RequestMethod::GET;
        }
        
        $method = filter_var($meth);
        
        if ($method !== false && in_array($method, RequestMethod::getAll())) {
            return $method;
        }
        
        return RequestMethod::GET;
    }

    private function extractHeaders() {
        $this->getHeadersPool()->clear();
        
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            
            foreach ($headers as $k => $v) {
                $this->addHeader($k, filter_var($v, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            }
        } else {
            $headersArr = $this->getRequestHeadersFromServer();
            
            foreach ($headersArr as $header) {
                $this->addHeader($header->getName(), $header->getValue());
            }
        }
    }

    private function filter($inputSource, $varName) {
        $val = filter_input($inputSource, $varName);
        
        if ($val === null) {
            if ($inputSource == INPUT_POST && isset($_POST[$varName])) {
                $val = filter_var(urldecode($_POST[$varName]));
            } else if ($inputSource == INPUT_GET && isset($_GET[$varName])) {
                $val = filter_var(urldecode($_GET[$varName]));
            } else if ($inputSource == INPUT_COOKIE && isset ($_COOKIE[$varName])) {
                $val = filter_var(urldecode($_COOKIE[$varName]));
            }
        }
        
        return $val;
    }

    private function getPathHelper(string $header) {
        $envVal = getenv($header);
        if ($envVal !== false) {
            return $envVal;
        }
        
        if (isset($_SERVER[$header])) {
            return $_SERVER[$header];
        }
        
        $headerVals = $this->getHeader($header);
        
        if (count($headerVals) == 1) {
            return $headerVals[0];
        }
        
        return null;
    }

    private function getRequestHeadersFromServer() : array {
        $retVal = [];
        
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $retVal[] = new HttpHeader($name, $value);
            }
        }
        
        return $retVal;
    }
}
