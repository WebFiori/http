<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2025 WebFiori Framework
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
    }
    
    /**
     * Adds new request method to the allowed methods.
     * 
     * @param string $method The request method (e.g. 'GET', 'POST', 'PUT', etc...).
     */
    public function addRequestMethod(string $method) {
        $normalizedMethod = strtoupper(trim($method));
        
        if (!in_array($normalizedMethod, $this->allowedMethods)) {
            $this->allowedMethods[] = $normalizedMethod;
        }
    }
    
    /**
     * Adds a value to URI parameter.
     * 
     * @param string $paramName The name of the parameter.
     * @param string $value The value to add.
     */
    public function addVarValue(string $paramName, string $value) {
        $uriVars = $this->getComponents()['uri-vars'];
        
        foreach ($uriVars as $param) {
            if ($param->getName() == $paramName) {
                $param->setValue($value);
                break;
            }
        }
    }
    
    /**
     * Checks if two URIs are equal or not.
     * 
     * @param RequestUri $otherUri An object of type 'RequestUri'.
     * 
     * @return bool The method will return true if the URIs are 
     * equal. False if not.
     */
    public function equals(RequestUri $otherUri) : bool {
        $thisPath = $this->getPath();
        $otherPath = $otherUri->getPath();
        
        if ($thisPath != $otherPath) {
            return false;
        }
        
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
     * Returns the value of URI parameter given its name.
     * 
     * @param string $paramName The name of the parameter.
     * 
     * @return UriParameter|null If a parameter which has the given name 
     * is found, it will be returned. If no such parameter, the method will 
     * return null.
     */
    public function getParameter(string $paramName) {
        $uriVars = $this->getComponents()['uri-vars'];
        
        foreach ($uriVars as $param) {
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
        return $this->getComponents()['uri-vars'];
    }
    
    /**
     * Returns an associative array that contains URI parameters and their values.
     * 
     * @return array An associative array. The keys will be parameters names 
     * and the values will be sub-arrays that contains possible values.
     */
    public function getParameterValues() : array {
        $retVal = [];
        $uriVars = $this->getComponents()['uri-vars'];
        
        foreach ($uriVars as $param) {
            $retVal[$param->getName()] = $param->getValue();
        }
        
        return $retVal;
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
     * Checks if the URI has any parameters or not.
     * 
     * @return bool The method will return true if the URI has any parameters. 
     * false if not.
     */
    public function hasParameters() : bool {
        return count($this->getParameters()) > 0;
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
     * Checks if all URI parameters have values or not.
     * 
     * @return bool The method will return true if all URI parameters 
     * have values. false if not.
     */
    public function isAllParametersSet() : bool {
        $uriVars = $this->getComponents()['uri-vars'];
        
        foreach ($uriVars as $param) {
            if ($param->getValue() === null) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Checks if a request method is allowed or not.
     * 
     * @param string $method The request method (e.g. 'GET', 'POST', 'PUT', etc...).
     * 
     * @return bool The method will return true if the method is allowed. 
     * If no request methods are specified, the method will return true. 
     * Other than that, the method will return false.
     */
    public function isRequestMethodAllowed(string $method) : bool {
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
    public function setParameterValue(string $paramName, string $value) {
        $param = $this->getParameter($paramName);
        
        if ($param !== null) {
            $param->setValue($value);
        }
    }
    
    /**
     * Sets the array of allowed request methods.
     * 
     * @param array $methods An array that contains request methods names.
     */
    public function setRequestMethods(array $methods) {
        $this->allowedMethods = [];
        
        foreach ($methods as $method) {
            $this->addRequestMethod($method);
        }
    }
}
