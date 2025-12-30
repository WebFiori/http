<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2025-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use InvalidArgumentException;

/**
 * A class for representing request URIs with routing capabilities.
 *
 * @author Ibrahim
 */
class RequestUri extends Uri {
    /**
     * An array that contains the names of allowed request methods.
     * 
     * @var array
     */
    private $allowedMethods;
    private $vars;
    /**
     * Creates new instance of the class.
     * 
     * @param string $requestedUri The URI such as 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=2018#xyz'
     * 
     * @throws InvalidArgumentException
     */
    public function __construct(string $requestedUri = '') {
        parent::__construct($requestedUri);
        $this->allowedMethods = [];
        $this->vars = [];
        $addedParams = [];
        $pathArr = $this->getPathArray();


        foreach ($pathArr as $part) {
            $conv = mb_convert_encoding(urldecode($part), 'UTF-8', 'ISO-8859-1');

            if ($conv[0] == '{' && $conv[strlen($conv) - 1] == '}') {
                $name = trim($part, '{}');

                if (!in_array($name, $addedParams)) {
                    $addedParams[] = $name;
                    $this->vars[] = new UriParameter($name);
                }
            }
        }
        $this->verifyOrderOfParams();
    }

    /**
     * Adds a value to allowed URI parameter values.
     * 
     * @param string $paramName The name of the parameter.
     * @param string $value The value to add.
     */
    public function addAllowedParameterValue(string $paramName, string $value) : RequestUri {
        $normalized = trim($paramName);

        foreach ($this->getParameters() as $param) {
            if ($param->getName() == $normalized) {
                $param->addAllowedValue($value);
                break;
            }
        }

        return $this;
    }
    public function addAllowedParameterValues(string $name, array $vals) : RequestUri {
        $normalizedName = trim($name);

        foreach ($this->getParameters() as $param) {
            if ($param->getName() == $normalizedName) {
                $param->addAllowedValues($vals);
                break;
            }
        }

        return $this;
    }

    /**
     * Adds new request method to the allowed methods.
     * 
     * @param string $method The request method (e.g. 'GET', 'POST', 'PUT', etc...).
     */
    public function addRequestMethod(string $method) : RequestUri {
        $normalizedMethod = strtoupper(trim($method));

        if (!in_array($normalizedMethod, $this->allowedMethods)) {
            $this->allowedMethods[] = $normalizedMethod;
        }

        return $this;
    }

    /**
     * Checks if two URIs are equal or not.
     * 
     * @param RequestUri $otherUri An object of type 'RequestUri'.
     * 
     * @return bool The method will return true if the URIs are 
     * equal. False if not.
     */
    public function equals(Uri $otherUri) : bool {
        if (!parent::equals($otherUri)) {
            return false;
        }
        $thisPath = $this->getPath();
        $otherPath = $otherUri->getPath();

        $thisMethods = $this->getRequestMethods();
        $otherMethods = $otherUri->getRequestMethods();

        if (count($thisMethods) != count($otherMethods)) {
            return false;
        }

        foreach ($thisMethods as $method) {
            if (!in_array($method, $otherMethods)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array that contains allowed URI parameters values.
     * 
     * @return array
     */
    public function getAllowedParameterValues(string $varName) : array {
        $param = $this->getParameter($varName);

        if ($param !== null) {
            return $param->getAllowedValues();
        }

        return [];
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
     */
    public static function getBaseURL(?array $serverInputs = null) : string {
        $server = $serverInputs ?? $_SERVER;
        $tempHost = $server['HTTP_HOST'] ?? '127.0.0.1';
        $host = trim(filter_var($tempHost),'/');

        if (isset($server['HTTPS'])) {
            $secureHost = filter_var($server['HTTPS']);
        } else {
            $secureHost = '';
        }
        $protocol = 'http://';
        $useHttp = defined('USE_HTTP') && USE_HTTP === true;

        if (strlen($secureHost) != 0 && !$useHttp) {
            $protocol = "https://";
        }

        if (isset($server['DOCUMENT_ROOT'])) {
            $docRoot = filter_var($server['DOCUMENT_ROOT']);
        } else {
            //Fix for IIS since the $_SERVER['DOCUMENT_ROOT'] is not set
            //in some cases
            $docRoot = getcwd();
        }

        $docRootLen = strlen($docRoot);

        if ($docRootLen == 0) {
            $docRoot = __DIR__;
            $docRootLen = strlen($docRoot);
        }

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', __DIR__);
        }
        $toAppend = str_replace('\\', '/', substr(ROOT_PATH, $docRootLen, strlen(ROOT_PATH) - $docRootLen));

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
     * Returns the value of URI parameter given its name.
     * 
     * @param string $paramName The name of the parameter.
     * 
     * @return UriParameter|null If a parameter which has the given name 
     * is found, it will be returned. If no such parameter, the method will 
     * return null.
     */
    public function getParameter(string $paramName) : ?UriParameter {
        foreach ($this->getParameters() as $param) {
            if ($param->getName() == $paramName) {
                return $param;
            }
        }

        return null;
    }

    /**
     * Returns an array that contains all URI parameters.
     * 
     * @return array An array that contains objects of type 'UriParameter'.
     */
    public function getParameters() : array {
        return $this->vars;
    }
    public function getParametersNames() : array {
        return array_map(function ($paramObj)
        {
            return $paramObj->getName();
        }, $this->getParameters());
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
     */
    public function getParameterValue(string $varName) : ?string {
        $param = $this->getParameter($varName);

        if ($param !== null) {
            return $param->getValue();
        }

        return null;
    }

    /**
     * Returns an array that contains all allowed request methods.
     * 
     * @return array An array that contains all allowed request methods.
     */
    public function getRequestMethods() : array {
        return $this->allowedMethods;
    }

    /**
     * Checks if the URI has a specific parameter or not.
     * 
     * @param string $paramName The name of the parameter.
     * 
     * @return bool The method will return true if the parameter 
     * is found. false if not.
     */
    public function hasParameter(string $paramName) : bool {
        return $this->getParameter($paramName) !== null;
    }

    /**
     * Checks if the URI has any parameters or not.
     * 
     * @return bool The method will return true if the URI has any parameters. 
     * false if not.
     */
    public function hasParameters() : bool {
        return count($this->getParameters()) > 0;
    }

    /**
     * Checks if all URI parameters have values or not.
     * 
     * @return bool The method will return true if all URI parameters 
     * have values. false if not.
     */
    public function isAllParametersSet() : bool {
        $uriVars = $this->getParameters();

        foreach ($uriVars as $param) {
            if ($param->getValue() === null && !$param->isOptional()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if a request method is allowed or not.
     * 
     * @param string $method The request method (e.g. 'GET', 'POST', 'PUT', etc...).
     * If not provided, the method will attempt to get request method using the environment
     * variable 'REQUEST_METHOD'.
     * 
     * @return bool The method will return true if the method is allowed. 
     * If no request methods are specified, the method will return true. 
     * Other than that, the method will return false.
     */
    public function isRequestMethodAllowed(?string $method = null) : bool {
        if ($method === null) {
            $method = getenv('REQUEST_METHOD');

            if (!in_array($method, RequestMethod::getAll())) {
                return false;
            }
        }
        $normalizedMethod = strtoupper(trim($method));
        $methods = $this->getRequestMethods();

        return count($methods) == 0 || in_array($normalizedMethod, $methods);
    }

    /**
     * Sets the value of a URI parameter.
     * 
     * @param string $paramName The name of the parameter.
     * @param string $value The value to set.
     */
    public function setParameterValue(string $paramName, string $value) : bool {
        $param = $this->getParameter($paramName);

        if ($param !== null) {
            return $param->setValue($value);
        }

        return false;
    }

    /**
     * Sets the array of allowed request methods.
     * 
     * @param array $methods An array that contains request methods names.
     */
    public function setRequestMethods(array $methods) : RequestUri {
        $this->allowedMethods = [];

        foreach ($methods as $method) {
            $this->addRequestMethod($method);
        }

        return $this;
    }
    private function verifyOrderOfParams() {
        $currentOptional = false;

        foreach ($this->getParameters() as $param) {
            if ($currentOptional == true && !$param->isOptional()) {
                throw new \Exception('Requred paramater cannot appear after optional');
            }
            $currentOptional = $param->isOptional() || $currentOptional;
        }
    }
}
