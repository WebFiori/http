<?php

/* 
 * The MIT License
 *
 * Copyright 2019 Ibrahim BinAlshikh, restEasy library.
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

use webfiori\json\Json;
use webfiori\json\JsonI;
/**
 * A class that represents one web service.
 * 
 * A web service is simply an action that is performed by a web 
 * server to do something. For example, It is possible to have a web service 
 * which is responsible for creating new user profile. Think of it as an 
 * action taken to perform specific task.
 * 
 * @author Ibrahim
 * 
 * @version 1.0.3
 * 
 * @since 1.5.0 
 */
abstract class AbstractWebService implements JsonI {
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type error.
     * 
     * @since 1.0.2
     */
    const E = 'error';
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type info.
     * 
     * @since 1.0.2
     */
    const I = 'info';
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
     * The name of the service.
     * 
     * @var string
     * 
     * @since 1.0 
     */
    private $name;
    /**
     * The manager that the service belongs to.
     * 
     * @var WebServicesManager
     * 
     * @since 1.0.1 
     */
    private $owner;
    /**
     * An array that holds an objects of type RequestParameter.
     * 
     * @var array
     * 
     * @since 1.0 
     */
    private $parameters = [];
    /**
     * An array that contains service request methods.
     * 
     * @var array
     * 
     * @since 1.0 
     */
    private $reqMethods = [];
    /**
     * This is used to indicate if authentication is required when the service 
     * is called.
     * 
     * @var boolean
     * 
     * @since 1.0.1 
     */
    private $requreAuth;
    /**
     * An array that contains descriptions of 
     * possible responses.
     * 
     * @var array
     * 
     * @since 1.0
     */
    private $responses;
    /**
     * An optional description for the service.
     * 
     * @var sting
     * 
     * @since 1.0
     */
    private $serviceDesc;
    /**
     * An attribute that is used to tell since which API version the 
     * service was added.
     * 
     * @var string
     * 
     * @since 1.0 
     */
    private $sinceVersion;
    /**
     * Creates new instance of the class.
     * 
     * The developer can supply an optional service name. 
     * A valid service name must follow the following rules:
     * <ul>
     * <li>It can contain the letters [A-Z] and [a-z].</li>
     * <li>It can contain the numbers [0-9].</li>
     * <li>It can have the character '-' and the character '_'.</li>
     * </ul>
     * If The given name is invalid, the name of the service will be set to 'new-service'.
     * 
     * @param string $name The name of the web service. 
     */
    public function __construct($name) {
        if (!$this->setName($name)) {
            $this->setName('new-service');
        }
        $this->reqMethods = [];
        $this->parameters = [];
        $this->responses = [];
        $this->requreAuth = true;
    }
    /**
     * Returns an array that contains all possible requests methods at which the 
     * service can be called with.
     * 
     * The array will contains strings like 'GET' or 'POST'. If no request methods 
     * where added, the array will be empty.
     * 
     * @return array An array that contains all possible requests methods at which the 
     * service can be called using.
     * 
     * @since 1.0
     */
    public function &getRequestMethods() {
        return $this->reqMethods;
    }
    /**
     * Returns an array that contains an objects of type RequestParameter.
     * 
     * @return array an array that contains an objects of type RequestParameter.
     * 
     * @since 1.0
     */
    public final function &getParameters() {
        return $this->parameters;
    }
    /**
     * 
     * @return string
     * 
     * @since 1.0
     */
    public function __toString() {
        $retVal = "APIAction[\n";
        $retVal .= "    Name => '".$this->getName()."',\n";
        $retVal .= "    Description => '".$this->getDescription()."',\n";
        $since = $this->getSince() === null ? 'null' : $this->getSince();
        $retVal .= "    Since => '$since',\n";
        $reqMethodsStr = "[\n";
        $comma = ',';

        for ($x = 0,  $count = count($this->getRequestMethods()) ; $x < $count ; $x++) {
            $meth = $this->getRequestMethods()[$x];

            if ($x + 1 == $count) {
                $comma = '';
            }
            $reqMethodsStr .= "        $meth$comma\n";
        }
        $reqMethodsStr .= "    ],\n";
        $retVal .= "    Request Methods => $reqMethodsStr";
        $paramsStr = "[\n";

        $comma = ',';

        for ($x = 0 , $count = count($this->getParameters()); $x < $count ; $x++) {
            $param = $this->getParameters()[$x];
            $paramsStr .= "        ".$param->getName()." => [\n";
            $paramsStr .= "            Type => '".$param->getType()."',\n";
            $descStr = $param->getDescription() === null ? 'null' : $param->getDescription();
            $paramsStr .= "            Description => '$descStr',\n";
            $isOptional = $param->isOptional() ? 'true' : 'false';
            $paramsStr .= "            Is Optional => '$isOptional',\n";
            $defaultStr = $param->getDefault() === null ? 'null' : $param->getDefault();
            $paramsStr .= "            Default => '$defaultStr',\n";
            $min = $param->getMinVal() === null ? 'null' : $param->getMinVal();
            $paramsStr .= "            Minimum Value => '$min',\n";
            $max = $param->getMaxVal() === null ? 'null' : $param->getMaxVal();

            if ($x + 1 == $count) {
                $comma = '';
            }
            $paramsStr .= "            Maximum Value => '$max'\n        ]$comma\n";
        }
        $paramsStr .= "    ],\n";
        $retVal .= "    Parameters => $paramsStr";
        $responsesStr = "[\n";
        $count = count($this->getResponsesDescriptions());
        $comma = ',';

        for ($x = 0 ; $x < $count ; $x++) {
            if ($x + 1 == $count) {
                $comma = '';
            }
            $responsesStr .= "        Response #$x => '".$this->getResponsesDescriptions()[$x]."'".$comma."\n";
        }
        $responsesStr .= "    ]\n";

        return $retVal."    Responses Descriptions => $responsesStr]\n";
    }
    /**
     * Adds new request parameter to the service.
     * 
     * The parameter will only be added if no parameter which has the same 
     * name as the given one is added before.
     * 
     * @param RequestParameter|array $param The parameter that will be added. It 
     * can be an object of type 'RequestParameter' or an associative array of 
     * options. The array can have the following indices:
     * <ul>
     * <li><b>name</b>: The name of the parameter. It must be provided.</li>
     * <li><b>type</b>: The datatype of the parameter. If not provided, 'string' is used.</li>
     * <li><b>optional</b>: A boolean. If set to true, it means the parameter is 
     * optional. If not provided, 'false' is used.</li>
     * <li><b>min</b>: Minimum value of the parameter. Applicable only for 
     * numeric types.</li>
     * <li><b>max</b>: Maximum value of the parameter. Applicable only for 
     * numeric types.</li>
     * <li><b>allow-empty</b>: A boolean. If the type of the parameter is string or string-like 
     * type and this is set to true, then empty strings will be allowed. If 
     * not provided, 'false' is used.</li>
     * <li><b>custom-filter</b>: A PHP function that can be used to filter the 
     * parameter even further</li>
     * <li><b>default</b>: An optional default value to use if the parameter is 
     * not provided and is optional.</li>
     * <li><b>description</b>: The description of the attribute.</li>
     * </ul>
     * 
     * @return boolean If the given request parameter is added, the method will 
     * return true. If it was not added for any reason, the method will return 
     * false.
     * 
     * @since 1.0
     */
    public function addParameter($param) {
        if (gettype($param) == 'array') {
            $param = RequestParameter::createParam($param);
        }

        if ($param instanceof RequestParameter && !$this->hasParameter($param->getName())) {
            $this->parameters[] = $param;

            return true;
        }

        return false;
    }
    /**
     * Adds multiple parameters to the web service in one batch.
     * 
     * @param array $params An associative or indexed array. If the array is indexed, 
     * each index should hold an object of type 'RequestParameter'. If it is associative,
     * then the key will represent the name of the web service and the value of the 
     * key should be a sub-associative array that holds parameter options.
     * 
     * @since 1.0.3
     */
    public function addParameters(array $params) {
        foreach ($params as $paramIndex => $param) {
            if ($param instanceof RequestParameter) {
                $this->addParameter($param);
            } else if (gettype($param) == 'array') {
                $param['name'] = $paramIndex;
                $this->addParameter(RequestParameter::createParam($param));
            }
        }
    }
    /**
     * Adds new request method.
     * 
     * The value that will be passed to this method can be any string 
     * that represents HTTP request method (e.g. 'get', 'post', 'options' ...). It 
     * can be in upper case or lower case.
     * 
     * @param string $method The request method.
     * 
     * @return boolean true in case the request method is added. If the given 
     * request method is already added or the method is unknown, the method 
     * will return false.
     * 
     * @since 1.0
     */
    public final function addRequestMethod($method) {
        $uMethod = strtoupper(trim($method));

        if (in_array($uMethod, self::METHODS) && !in_array($uMethod, $this->reqMethods)) {
            $this->reqMethods[] = $uMethod;

            return true;
        }

        return false;
    }
    /**
     * Adds response description.
     * 
     * It is used to describe the API for front-end developers and help them 
     * identify possible responses if they call the API using the specified service.
     * 
     * @param string $description A paragraph that describes one of 
     * the possible responses due to calling the service.
     * 
     * @since 1.0
     */
    public final function addResponseDescription($description) {
        $trimmed = trim($description);

        if (strlen($trimmed) != 0) {
            $this->responses[] = $trimmed;
        }
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
     *  @since 1.0.1
     */
    public function getAuthHeader() {
        return Request::getAuthHeader();
    }
    /**
     * Returns the description of the service.
     * 
     * @return string|null The description of the service. If the description is 
     * not set, the method will return null.
     * 
     * @since 1.0
     */
    public final function getDescription() {
        return $this->serviceDesc;
    }
    /**
     * Returns an associative array or an object of type Json of filtered request inputs.
     * 
     * The indices of the array will represent request parameters and the 
     * values of each index will represent the value which was set in 
     * request body. The values will be filtered and might not be exactly the same as 
     * the values passed in request body. Note that if a parameter is optional and not 
     * provided in request body, its value will be set to 'null'. Note that 
     * if request content type is 'application/json', only basic filtering will 
     * be applied. Also, parameters in this case don't apply.
     * 
     * @return array|Json|null An array of filtered request inputs. This also can 
     * be an object of type 'Json' if request content type was 'application/json'. 
     * If no manager was associated with the service, the method will return null.
     * 
     * @since 1.0.1
     */
    public function getInputs() {
        $manager = $this->getManager();

        if ($manager !== null) {
            return $manager->getInputs();
        }
    }
    /**
     * 
     * @return WebServicesManager|null
     */
    public function getManager() {
        return $this->owner;
    }
    /**
     * Returns the name of the service.
     * 
     * @return string The name of the service.
     * 
     * @since 1.0
     */
    public final function getName() {
        return $this->name;
    }
    /**
     * Returns one of the parameters of the service given its name.
     * 
     * @param string $paramName The name of the parameter.
     * 
     * @return RequestParameter|null Returns an objects of type RequestParameter if 
     * a parameter with the given name was found. null if nothing is found.
     * 
     * @since 1.0
     */
    public final function getParameterByName($paramName) {
        $trimmed = trim($paramName);

        if (strlen($trimmed) != 0) {
            foreach ($this->parameters as $param) {
                if ($param->getName() == $trimmed) {
                    return $param;
                }
            }
        }

        return null;
    }
    /**
     * Returns the value of request parameter given its name.
     * 
     * @param string $paramName The name of request parameter as specified when 
     * it was added to the service.
     * 
     * @return mixed|null If the parameter is found and its value is set, the 
     * method will return its value. Other than that, the method will return null. 
     * For optional parameters, if a default value is set for it, the method will
     * return that value.
     * 
     * @since 1.0.1
     */
    public function getParamVal($paramName) {
        $inputs = $this->getInputs();
        $trimmed = trim($paramName);

        if ($inputs !== null) {
            if ($inputs instanceof Json) {
                return $inputs->get($trimmed);
            } else {
                return isset($inputs[$trimmed]) ? $inputs[$trimmed] : null;
            }
        }
    }
    /**
     * Returns an indexed array that contains information about possible responses.
     * 
     * It is used to describe the API for front-end developers and help them 
     * identify possible responses if they call the API using the specified service.
     * 
     * @return array An array that contains information about possible responses.
     * 
     * @since 1.0
     */
    public final function getResponsesDescriptions() {
        return $this->responses;
    }
    /**
     * Returns version number or name at which the service was added to the API.
     * 
     * Version number is set based on the version number which was set in the 
     * class WebAPI.
     * 
     * @return string The version number at which the service was added to the API.
     * 
     * @since 1.0
     */
    public final function getSince() {
        return $this->sinceVersion;
    }
    /**
     * Checks if the service has a specific request parameter given its name.
     * 
     * Note that the name of the parameter is case sensitive. This means that 
     * 'get-profile' is not the same as 'Get-Profile'.
     * 
     * @param string $name The name of the parameter.
     * 
     * @return boolean If a request parameter which has the given name is added 
     * to the service, the method will return true. Otherwise, the method will return 
     * false.
     * 
     * @since 1.0
     */
    public function hasParameter($name) {
        $trimmed = trim($name);

        if (strlen($name) != 0) {
            foreach ($this->getParameters() as $param) {
                if ($param->getName() == $trimmed) {
                    return true;
                }
            }
        }

        return false;
    }
    /**
     * Checks if the client is authorized to use the service or not.
     * 
     * The developer should implement this method in a way it returns a boolean. 
     * If the method returns true, it means the client is allowed to use the service. 
     * If the method returns false, then he is not authorized and a 401 error 
     * code will be sent back. If the method returned nothing, then it means the 
     * user is authorized to call the API. If WebFiori framework is used, it is 
     * possible to perform the functionality of this method using middleware.
     * 
     * @since 1.0.1
     */
    public function isAuthorized() {
        
    }
    /**
     * Returns the value of the property 'requreAuth'.
     * 
     * The property is used to tell if the authorization step will be skipped 
     * or not when the service is called. 
     * 
     * @return boolean The method will return true if authorization step required. 
     * False if the authorization step will be skipped. Default return value is true.
     * 
     * @since 1.0.1
     */
    public function isAuthRequred() {
        return $this->requreAuth;
    }
    /**
     * Process client's request.
     * 
     * This method must be implemented in a way it sends back a response after 
     * processing the request.
     * 
     * @since 1.0.1
     */
    abstract function processRequest();
    /**
     * Removes a request parameter from the service given its name.
     * 
     * @param string $paramName The name of the parameter (case sensitive).
     * 
     * @return null|RequestParameter If a parameter which has the given name 
     * was removed, the method will return an object of type 'RequestParameter' 
     * that represents the removed parameter. If nothing is removed, the 
     * method will return null.
     * 
     * @since 1.0
     */
    public function removeParameter($paramName) {
        $trimmed = trim($paramName);
        $params = &$this->getParameters();
        $index = -1;
        $count = count($params);

        for ($x = 0 ; $x < $count ; $x++) {
            if ($params[$x]->getName() == $trimmed) {
                $index = $x;
                break;
            }
        }
        $retVal = null;

        if ($index != -1) {
            if ($count == 1) {
                $retVal = $params[0];
                unset($params[0]);
            } else {
                $retVal = $params[$index];
                $params[$index] = $params[$count - 1];
                unset($params[$count - 1]);
            }
        }

        return $retVal;
    }
    /**
     * Removes a request method from the previously added ones. 
     * 
     * @param string $method The request method (e.g. 'get', 'post', 'options' ...). It 
     * can be in upper case or lower case.
     * 
     * @return boolean If the given request method is remove, the method will 
     * return true. Other than that, the method will return true.
     * 
     * @since 1.0
     */
    public function removeRequestMethod($method) {
        $uMethod = strtoupper(trim($method));
        $allowedMethods = &$this->getRequestMethods();

        if (in_array($uMethod, $allowedMethods)) {
            $count = count($allowedMethods);
            $methodIndex = -1;

            for ($x = 0 ; $x < $count ; $x++) {
                if ($this->getRequestMethods()[$x] == $uMethod) {
                    $methodIndex = $x;
                    break;
                }
            }

            if ($count == 1) {
                unset($allowedMethods[0]);
            } else {
                $allowedMethods[$methodIndex] = $allowedMethods[$count - 1];
                unset($allowedMethods[$count - 1]);
            }

            return true;
        }

        return false;
    }
    /**
     * Sends Back a data using specific content type and specific response code.
     * 
     * @param string $conentType Response content type (such as 'application/json')
     * 
     * @param mixed $data Any data to send back. Mostly, it will be a string.
     * 
     * @param int $code HTTP response code that will be used to send the data. 
     * Default is HTTP code 200 - Ok.
     * 
     * @since 1.0.1
     */
    public function send($conentType, $data, $code = 200) {
        $manager = $this->getManager();

        if ($manager !== null) {
            $manager->send($conentType, $data, $code);
        }
    }
    /**
     * Sends a JSON response to the client.
     * 
     * The basic format of the message will be as follows:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Action is not set.",<br/>
     * &nbsp;&nbsp;"type":"error"<br/>
     * &nbsp;&nbsp;"http-code":404<br/>
     * &nbsp;&nbsp;"more-info":EXTRA_INFO<br/>
     * }
     * </p>
     * Where EXTRA_INFO can be a simple string or any JSON data.
     * 
     * @param string $message The message to send back.
     * 
     * @param string $type A string that tells the client what is the type of 
     * the message. The developer can specify his own message types such as 
     * 'debug', 'info' or any string. If it is empty string, it will be not 
     * included in response payload.
     * 
     * @param int $code Response code (such as 404 or 200). Default is 200.
     * 
     * @param mixed $otherInfo Any other data to send back it can be a simple 
     * string, an object... . If null is given, the parameter 'more-info' 
     * will be not included in response. Default is empty string. Default is null.
     * 
     * @since 1.0.1
     */
    public function sendResponse($message,$type = '',$code = 200,$otherInfo = null) {
        $manager = $this->getManager();

        if ($manager !== null) {
            $manager->sendResponse($message, $type, $code, $otherInfo);
        }
    }
    /**
     * Sets the description of the service.
     * 
     * Used to help front-end to identify the use of the service.
     * 
     * @param sting $desc Action description.
     * 
     * @since 1.0
     */
    public final function setDescription($desc) {
        $this->serviceDesc = trim($desc);
    }
    /**
     * Sets the value of the property 'requreAuth'.
     * 
     * The property is used to tell if the authorization step will be skipped 
     * or not when the service is called. 
     * 
     * @param boolean $bool True to make authorization step required. False to 
     * skip the authorization step.
     * 
     * @since 1.0.1
     */
    public function setIsAuthRequred($bool) {
        $this->requreAuth = $bool === true;
    }
    /**
     * Associate the web service with a manager.
     * 
     * The developer does not have to use this method. It is used when a 
     * service is added to a manager.
     * 
     * @param WebServicesManager|null $manager The manager at which the service 
     * will be associated with. If null is given, the association will be removed if 
     * the service was associated with a manager.
     * 
     */
    public function setManager($manager) {
        if ($manager === null) {
            $this->owner = null;
        } else {
            if ($manager instanceof WebServicesManager) {
                $this->owner = $manager;
            }
        }
    }
    /**
     * Sets the name of the service.
     * 
     * A valid service name must follow the following rules:
     * <ul>
     * <li>It can contain the letters [A-Z] and [a-z].</li>
     * <li>It can contain the numbers [0-9].</li>
     * <li>It can have the character '-' and the character '_'.</li>
     * </ul>
     * 
     * @param string $name The name of the web service.
     * 
     * @return boolean If the given name is valid, the method will return 
     * true once the name is set. false is returned if the given 
     * name is invalid.
     * 
     * @since 1.0
     */
    public final function setName($name) {
        $trimmedName = trim($name);
        $len = strlen($trimmedName);

        if ($len != 0) {
            for ($x = 0 ; $x < $len ; $x++) {
                $ch = $trimmedName[$x];

                if (!($ch == '_' || $ch == '-' || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z') || ($ch >= '0' && $ch <= '9'))) {
                    return false;
                }
            }
            $this->name = $name;

            return true;
        }

        return false;
    }
    /**
     * Sets version number or name at which the service was added to a manager.
     * 
     * This method is called automatically when the service is added to any 
     * services manager. The developer does not have to use this method.
     * 
     * @param string The version number at which the service was added to the API.
     * 
     * @since 1.0
     */
    public final function setSince($sinceAPIv) {
        $this->sinceVersion = $sinceAPIv;
    }
    /**
     * Returns a Json object that represents the service.
     * 
     * The generated JSON string from the returned Json object will have 
     * the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"name":"",<br/>
     * &nbsp;&nbsp;"since":"",<br/>
     * &nbsp;&nbsp;"description":"",<br/>
     * &nbsp;&nbsp;"request-methods":[],<br/>
     * &nbsp;&nbsp;"parameters":[],<br/>
     * &nbsp;&nbsp;"responses":[]<br/>
     * }
     * </p>
     * 
     * @return Json an object of type Json.
     * 
     * @since 1.0
     */
    public function toJSON() {
        $json = new Json();
        $json->add('name', $this->getName());
        $json->add('since', $this->getSince());
        $json->add('description', $this->getDescription());
        $json->add('request-methods', $this->reqMethods);
        $json->add('parameters', $this->parameters);
        $json->add('responses', $this->getResponsesDescriptions());

        return $json;
    }
}
