<?php

/*
 * The MIT License
 *
 * Copyright 2019 Ibrahim, WebFiori HTTP.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace webfiori\http;

use InvalidArgumentException;
/**
 * A class that is used to split URIs and get their parameters.
 * 
 * The main aim of this class is to extract URI parameters including:
 * <ul>
 * <li>Host</li>
 * <li>Authority</li>
 * <li>Fragment (if any)</li>
 * <li>Path</li>
 * <li>Port (if any)</li>
 * <li>Query string (if any)</li>
 * <li>Scheme</li>
 * </ul>
 * For more information on URI structure, visit <a target="_blank" href="https://en.wikipedia.org/wiki/Uniform_Resource_Identifier#Examples">Wikipedia</a>.
 * 
 * @author Ibrahim
 * 
 * @version 1.0.1
 */
class Uri {
    /**
     *
     * @var array 
     * 
     * @since 1.0.1
     */
    private $allowedRequestMethods;
    /**
     * The URI broken into its sub-components (scheme, authority ...) as an associative 
     * array.
     * @var array 
     * @since 1.0
     */
    private $uriBroken;
    /**
     * Creates new instance.
     * 
     * @param string $requestedUri The URI such as 'https://www3.webfiori.com:80/{some-var}/hell/{other-var}/?do=dnt&y=#xyz'
     */
    public function __construct(string $requestedUri) {
        $this->allowedRequestMethods = [];
        $this->isCS = false;
        $this->uriBroken = self::splitURI($requestedUri);

        if (gettype($this->uriBroken) != 'array') {
            throw new InvalidArgumentException('Invalid URI given.');
        }
        
        if (!$this->checkOptionalParamsOrder()) {
            throw  new InvalidArgumentException('Inncorrect parameters order.');
        }
        $this->uriBroken['vars-possible-values'] = [];

        foreach ($this->getParametersNames() as $varName) {
            $this->uriBroken['vars-possible-values'][$varName] = [];
        }
    }
    private function checkOptionalParamsOrder() {
        $hasOptional = false;
        
        foreach ($this->getParameters() as $obj) {
            $hasOptional = $hasOptional || $obj->isOptional();
            if ($hasOptional && !$obj->isOptional()) {
                
                return false;
            }
        }
        
        return true;
    }

    /**
     * Adds a request method to the allowed set of methods at which the URI can 
     * be called with.
     * 
     * @param string $method A string such as 'GET' or 'POST'. Note that the 
     * value must exist in the array Request::METHODS or it will be not added.
     * 
     * @since 1.0.1
     */
    public function addRequestMethod(string $method) {
        if (in_array($method, Request::METHODS)) {
            $this->allowedRequestMethods[] = $method;
        }
    }
    /**
     * Adds a possible value for a URI parameter.
     * 
     * This is used in constructing the sitemap node of the URI. If a value is 
     * provided, then it will be part of the URI that will appear in the sitemap.
     * 
     * @param string $varName The name of the parameter. It must be exist as 
     * the path part in the URI.
     * 
     * @param string $varValue The value of the parameter. Note that any extra spaces 
     * in the value will be trimmed.
     * 
     * @since 1.0
     */
    public function addVarValue(string $varName, string $varValue) {
        $trimmed = trim($varName);
        $trimmedVal = trim($varValue);

        if (strlen($trimmedVal) != 0 
                && isset($this->uriBroken['vars-possible-values'][$trimmed]) 
                && !in_array($trimmedVal, $this->uriBroken['vars-possible-values'][$trimmed])) {
            $this->uriBroken['vars-possible-values'][$trimmed][] = $trimmedVal;
        }
    }
    /**
     * Adds multiple values to URI parameter.
     * 
     * @param string $varName The name of the parameter.
     * 
     * @param array $arrayOfVals An array that contains all possible values for 
     * the parameter.
     * 
     * @since 1.0
     */
    public function addVarValues(string $varName, array $arrayOfVals) {
        if (gettype($arrayOfVals) == 'array') {
            foreach ($arrayOfVals as $val) {
                $this->addVarValue($varName, $val);
            }
        }
    }
    /**
     * Checks if two URIs are equal or not.
     * 
     * Two URIs are considered equal if they have the same authority and the 
     * same path name.
     * 
     * @param Uri $otherUri The URI which 'this' URI will be checked against. 
     * 
     * @return boolean The method will return true if the URIs are 
     * equal.
     * 
     * @since 1.0
     */
    public function equals(Uri $otherUri) : bool {
        if ($otherUri instanceof Uri) {
            $isEqual = true;

            if ($this->getAuthority() == $otherUri->getAuthority()) {
                $thisPathNames = $this->getPathArray();
                $otherPathNames = $otherUri->getPathArray();
                $boolsArr = [];

                foreach ($thisPathNames as $path1) {
                    $boolsArr[] = in_array($path1, $otherPathNames);
                }

                foreach ($otherPathNames as $path) {
                    $boolsArr[] = in_array($path, $thisPathNames);
                }

                foreach ($boolsArr as $bool) {
                    $isEqual = $isEqual && $bool;
                }

                return $isEqual;
            }
        }

        return false;
    }
    /**
     * Returns authority part of the URI.
     * 
     * @return string The authority part of the URI. Usually, 
     * it is a string in the form '//www.example.com:80'.
     * 
     * @since 1.0
     */
    public function getAuthority() : string {
        return $this->uriBroken['authority'];
    }
    /**
     * Returns the base URL of the framework.
     * 
     * The returned value will depend on the folder where the library files 
     * are located. For example, if your domain is 'example.com' and the library 
     * is placed at the root and the requested resource is 'http://example.com/x/y/z', 
     * then the base URL will be 'http://example.com/'. If the library is 
     * placed inside a folder in the server which has the name 'system', and 
     * the same resource is requested, then the base URL will be 
     * 'http://example.com/system'.
     * 
     * @return string The base URL (such as 'http//www.example.com/')
     * 
     * @since 0.2
     */
    public static function getBaseURL() : string {
        $tempHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1';
        $host = trim(filter_var($tempHost),'/');

        if (isset($_SERVER['HTTPS'])) {
            $secureHost = filter_var($_SERVER['HTTPS']);
        } else {
            $secureHost = '';
        }
        $protocol = 'http://';
        $useHttp = defined('USE_HTTP') && USE_HTTP === true;

        if (strlen($secureHost) != 0 && !$useHttp) {
            $protocol = "https://";
        }
        $docRoot = filter_var($_SERVER['DOCUMENT_ROOT']);
        $docRootLen = strlen($docRoot);

        if ($docRootLen == 0) {
            $docRoot = __DIR__;
            $docRootLen = strlen($docRoot);
        }

        if (!defined('ROOT_DIR')) {
            define('ROOT_DIR', __DIR__);
        }
        $toAppend = str_replace('\\', '/', substr(ROOT_DIR, $docRootLen, strlen(ROOT_DIR) - $docRootLen));

        if (defined('WF_PATH_TO_REMOVE')) {
            $toAppend = str_replace(str_replace('\\', '/', WF_PATH_TO_REMOVE),'' ,$toAppend);
        }
        $xToAppend = str_replace('\\', '/', $toAppend);

        if (defined('WF_PATH_TO_APPEND')) {
            $xToAppend = $xToAppend.'/'.trim(str_replace('\\', '/', WF_PATH_TO_APPEND), '/');
        }

        if (strlen($xToAppend) == 0) {
            return $protocol.$host;
        } else {
            return $protocol.$host.'/'.trim($xToAppend, '/');
        }
    }
    /**
     * Returns an associative array which contains all URI parts.
     * 
     * @return array The method will return an associative array that 
     * contains the components of the URI. The array will have the 
     * following indices:
     * <ul>
     * <li><b>uri</b>: The original URI.</li>
     * <li><b>port</b>: The port number taken from the authority part.</li>
     * <li><b>host</b>: Will be always empty string.</li>
     * <li><b>authority</b>: Authority part of the URI.</li>
     * <li><b>scheme</b>: Scheme part of the URI (e.g. http or https).</li>
     * <li><b>query-string</b>: Query string if the URI has any.</li>
     * <li><b>fragment</b>: Any string that comes after the character '#' in the URI.</li>
     * <li><b>path</b>: An array that contains the names of path directories</li>
     * <li><b>query-string-vars</b>: An array that contains query string parameter and values.</li>
     * <li><b>uri-vars</b>: An array that contains URI path parameter and values.</li>
     * </ul>
     * 
     * @since 1.0
     */
    public function getComponents() : array {
        return $this->uriBroken;
    }
    /**
     * Returns fragment part of the URI.
     * 
     * @return string Fragment part of the URI. The fragment in the URI is 
     * any string that comes after the character '#'.
     * 
     * @since 1.0
     */
    public function getFragment() : string {
        return $this->uriBroken['fragment'];
    }
    /**
     * Returns host name from the host part of the URI.
     * 
     * @return string The host name such as 'www.webfiori.com'.
     * 
     * @since 1.0
     */
    public function getHost() : string {
        return $this->uriBroken['host'];
    }
    /**
     * Returns URI parameter given its name.
     * 
     * @param string $name The name of the parameter.
     * 
     * @return UriParameter|null If such parameter is found, it will be returned
     * as an object. Other than that, null is returned.
     */
    public function getParameter(string $name) {
        foreach ($this->getParameters() as $obj) {
            if ($obj->getName() == $name) {
                return $obj;
            }
        }
    }
    /**
     * Returns an array which contains URI parameters as objects.
     * 
     * @return array An indexed array which contains URI parameters as 
     * objects of type UriParameter.
     * 
     * @since 1.0
     */
    public function getParameters() : array {
        return $this->uriBroken['uri-vars'];
    }
    public function getParametersNames() : array {
        $retVal = [];

        foreach ($this->getParameters() as $paramObj) {
            $retVal[] = $paramObj->getName();
        }

        return $retVal;
    }
    /**
     * Returns the path part of the URI.
     * 
     * @return string A string such as '/path1/path2/path3'.
     * 
     * @since 1.0
     */
    public function getPath() : string {
        $retVal = '';

        foreach ($this->uriBroken['path'] as $dir) {
            $retVal .= '/'.$dir;
        }

        return $retVal;
    }
    /**
     * Returns an array which contains the names of URI directories.
     * 
     * @return array An array which contains the names of URI directories. 
     * For example, if the path part of the URI is '/path1/path2', the 
     * array will contain the value 'path1' at index 0 and 'path2' at index 1.
     * 
     * @since 1.0
     */
    public function getPathArray() : array {
        return $this->uriBroken['path'];
    }
    /**
     * Returns port number of the authority part of the URI.
     * 
     * @return string Port number of the authority part of the URI. If 
     * port number was not specified, the method will return empty string.
     * 
     * @since 1.0
     */
    public function getPort() : string {
        return $this->uriBroken['port'];
    }
    /**
     * Returns the query string that was appended to the URI.
     * 
     * @return string The query string that was appended to the URI. 
     * If the URI has no query string, the method will return empty 
     * string.
     * 
     * @since 1.0
     */
    public function getQueryString() : string {
        return $this->uriBroken['query-string'];
    }
    /**
     * Returns an associative array which contains query string parameters.
     * 
     * @return array An associative array which contains query string parameters. 
     * the keys will be acting as the names of the parameters and the values 
     * of each parameter will be in its key.
     * 
     * @since 1.0
     */
    public function getQueryStringVars() : array {
        return $this->uriBroken['query-string-vars'];
    }
    /**
     * Returns an array that holds all allowed request methods at which the 
     * URI can be called with.
     * 
     * @return array An array that holds strings such as 'GET' or 'POST'.
     * 
     * @since 1.0.1
     */
    public function getRequestMethods() : array {
        return $this->allowedRequestMethods;
    }
    /**
     * Returns the scheme part of the URI.
     * 
     * @return string The scheme part of the URI. Usually, it is called protocol 
     * (like http, ftp).
     * 
     * @since 1.0
     */
    public function getScheme() : string {
        return $this->uriBroken['scheme'];
    }
    /**
     * Returns the original requested URI.
     * 
     * @param boolean $incQueryStr If set to true, the query string part 
     * will be included in the URL. Default is false.
     * 
     * @param boolean $incFragment If set to true, the fragment part 
     * will be included in the URL. Default is false.
     * 
     * @return string The original requested URI.
     * 
     * @since 1.0
     */
    public function getUri(bool $incQueryStr = false, bool $incFragment = false) {
        $retVal = $this->getScheme().':'.$this->getAuthority().$this->getPath();

        if ($incQueryStr === true && $incFragment === true) {
            $queryStr = $this->getQueryString();

            if (strlen($queryStr) != 0) {
                $retVal .= '?'.$queryStr;
            }
            $fragment = $this->getFragment();

            if (strlen($fragment) != 0) {
                $retVal .= '#'.$fragment;
            }
        } else {
            if ($incQueryStr === true && $incFragment === false) {
                $queryStr = $this->getQueryString();

                if (strlen($queryStr) != 0) {
                    $retVal .= '?'.$queryStr;
                }
            } else {
                if ($incQueryStr === false && $incFragment === true) {
                    $fragment = $this->getFragment();

                    if (strlen($fragment) != 0) {
                        $retVal .= '#'.$fragment;
                    }
                }
            }
        }

        return $retVal;
    }
    /**
     * Returns the value of URI parameter given its name.
     * 
     * A URI parameter is a string which is defined while creating the route. 
     * it is name is included between '{}'.
     * 
     * @param string $varName The name of the parameter. Note that this value 
     * must not include braces.
     * 
     * @return string|null The method will return the value of the 
     * parameter if found. If the parameter is not set or the parameter 
     * does not exist, the method will return null.
     * 
     * @since 1.0
     */
    public function getParameterValue(string $varName) {
        $param = $this->getParameter($varName);
        if ($param !== null) {
            return $param->getValue();
        }

        return null;
    }
    /**
     * Returns an array that contains possible values for a URI parameter.
     * 
     * @param string $varName The name of the parameter.
     * 
     * @return array The method will return an array that contains all possible 
     * values for the parameter which was added using the method Router::addUriVarValue(). 
     * If the parameter does not exist, the array will be empty.
     * 
     * @since 1.3.6
     */
    public function getParameterValues(string $varName) {
        $trimmed = trim($varName);

        if (isset($this->uriBroken['vars-possible-values'][$trimmed])) {
            return $this->uriBroken['vars-possible-values'][$trimmed];
        }

        return [];
    }
    /**
     * Checks if the URI has a parameter or not given its name.
     * 
     * A parameter is a string which is defined while creating the route. 
     * it is name is included between '{}'.
     * 
     * @param string $varName The name of the parameter.
     * 
     * @return boolean If the given parameter name is exist, the method will 
     * return true. Other than that, the method will return false.
     * 
     * @since 1.0
     */
    public function hasParameter(string $varName) : bool {
        return in_array($varName, $this->getParametersNames());
    }
    /**
     * Checks if the URI has any paramaters or not.
     * 
     * A parameter is a string which is defined while creating the route. 
     * it is name is included between '{}'.
     * 
     * @return bool If the URI has any parameters, the method will 
     * return true.
     * 
     * @since 1.0
     */
    public function hasParameters() : bool {
        return count($this->getParameters()) != 0;
    }
    /**
     * Checks if all URI parameters has values or not.
     * 
     * @return bool The method will return true if all non-optional URI 
     * parameters have a value other than null.
     * 
     * @since 1.0
     */
    public function isAllParametersSet() : bool {
        $canRoute = true;

        foreach ($this->getParameters() as $val) {
            $canRoute = $canRoute && ($val->isOptional() || $val->getValue() != null);
        }

        return $canRoute;
    }
    
    /**
     * Checks if URI is fetched using allowed request method or not.
     * 
     * @return boolean The method will return true in two cases, if the array 
     * that holds allowed request methods is empty or request method is exist 
     * in the allowed request methods. Other than that, the method will return 
     * false.
     * 
     * @since 1.0.1
     */
    public function isRequestMethodAllowed() : bool {
        $methods = $this->getRequestMethods();

        return count($methods) == 0 || in_array(Request::getMethod(), $this->getRequestMethods());
    }
    
    /**
     * Sets the value of a URI parameter.
     * 
     * A parameter is a string which is defined while creating the route. 
     * it is name is included between '{}'.
     * 
     * @param string $varName The name of the parameter.
     * 
     * @param string $value The value of the parameter.
     * 
     * @return bool The method will return true if the parameter 
     * was set. If the parameter does not exist, the method will return false.
     * 
     * @since 1.0
     */
    public function setParameterValue(string $varName, string $value) : bool {
        $param = $this->getParameter($varName);

        if ($param !== null) {
            $param->setValue($value);

            return true;
        }

        return false;
    }
    /**
     * Adds a set of request methods to the allowed methods at which the URI
     * can be called with.
     * 
     * @param array $methods An array that holds strings such as 'GET' or 'POST'.
     * 
     * @since 1.0.1
     */
    public function setRequestMethods(array $methods) {
        foreach ($methods as $m) {
            $this->addRequestMethod($m);
        }
    }
    /**
     * Breaks a URI into its basic components.
     * 
     * @param string $uri The URI that will be broken.
     * 
     * @return array|boolean If the given URI is not valid, 
     * the Method will return false. Other than that, The method will return an associative array that 
     * contains the components of the URI. The array will have the 
     * following indices:
     * <ul>
     * <li><b>uri</b>: The original URI.</li>
     * <li><b>port</b>: The port number taken from the authority part.</li>
     * <li><b>host</b>: Will be always empty string.</li>
     * <li><b>authority</b>: Authority part of the URI.</li>
     * <li><b>scheme</b>: Scheme part of the URI (e.g. http or https).</li>
     * <li><b>query-string</b>: Query string if the URI has any.</li>
     * <li><b>fragment</b>: Any string that comes after the character '#' in the URI.</li>
     * <li><b>path</b>: An array that contains the names of path directories</li>
     * <li><b>query-string-vars</b>: An array that contains query string parameter and values.</li>
     * <li><b>uri-vars</b>: An array that contains URI path parameters and values.</li>
     * </ul>
     * 
     * @since 1.0
     */
    public static function splitURI(string $uri) {
        $validate = filter_var(str_replace(' ', '%20', $uri),FILTER_VALIDATE_URL);

        if ($validate === false) {
            return false;
        }
        $retVal = [
            'uri' => $uri,
            'authority' => '',
            'host' => '',
            'port' => '',
            'scheme' => '',
            'query-string' => '',
            'fragment' => '',
            'path' => [],
            'query-string-vars' => [

            ],
            'uri-vars' => [

            ],
        ];
        //First step, extract the fragment
        $split1 = self::_queryOrFragment($uri, '#', '%23');
        $retVal['fragment'] = isset($split1[1]) ? $split1[1] : '';

        //after that, extract the query string
        $split1[0] = str_replace('?}', '<>', $split1[0]);
        $split2 = self::_queryOrFragment($split1[0], '?', '%3F');

        $retVal['query-string'] = isset($split2[1]) ? $split2[1] : '';

        $split2[0] = str_replace('<>', '?}', $split2[0]);
        //next comes the scheme
        $split3 = explode(':', $split2[0]);
        $retVal['scheme'] = $split3[0];

        if (count($split3) == 3) {
            //if 3, this means port number was specifyed in the URI
            $split3[1] = $split3[1].':'.$split3[2];
        }
        //now, break the remaining using / as a delemiter
        //the authority will be located at index 2 if the URI
        //follows the standatd
        $split4 = explode('/', $split3[1]);
        $retVal['authority'] = '//'.$split4[2];

        //after that, we create the path from the remaining parts
        //also we check if the path has parameters or not
        //a parameter is a value in the path which is enclosed between {}
        //optional parameter ends with ? (e.g. {name?}
        $addedParams = [];

        for ($x = 3 ; $x < count($split4) ; $x++) {
            $dirName = $split4[$x];

            if ($dirName != '') {
                $retVal['path'][] = mb_convert_encoding(urldecode($dirName), 'UTF-8', 'ISO-8859-1');

                if ($dirName[0] == '{' && $dirName[strlen($dirName) - 1] == '}') {
                    $name = trim($split4[$x], '{}');

                    if (!in_array($name, $addedParams)) {
                        $addedParams[] = $name;
                        $retVal['uri-vars'][] = new UriParameter($name);
                    }
                }
            }
        }
        //now extract port number from the authority (if any)
        $split5 = explode(':', $retVal['authority']);
        $retVal['port'] = isset($split5[1]) ? $split5[1] : '';
        //Also, host can be extracted at this step.
        $retVal['host'] = trim($split5[0],'//');
        //finaly, split query string and extract vars
        $split6 = explode('&', $retVal['query-string']);

        foreach ($split6 as $param) {
            $split7 = explode('=', $param);
            $retVal['query-string-vars'][$split7[0]] = isset($split7[1]) ? $split7[1] : '';
        }

        return $retVal;
    }
    
    /**
     * Splits a string based on character mask.
     * 
     * @param string $split The string to split.
     * 
     * @param string $char The character that the split is based on.
     * 
     * @param string $encoded The character when encoded in URI.
     * 
     * @return type
     */
    private static function _queryOrFragment($split, $char, $encoded) {
        $split2 = explode($char, $split);
        $spCount = count($split2);

        if ($spCount > 2) {
            $temp = [];

            for ($x = 0 ; $x < $spCount - 1 ; $x++) {
                $temp[] = $split2[$x];
            }
            $lastStr = $split2[$spCount - 1];

            if (strlen($lastStr) == 0) {
                $split2 = [
                    implode($encoded, $temp).$encoded
                ];
            } else {
                $split2 = [
                    implode($encoded, $temp),
                    $split2[$spCount - 1]
                ];
            }
        }

        return $split2;
    }
}
