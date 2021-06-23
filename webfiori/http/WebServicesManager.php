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
 * A class that is used to manage multiple web services.
 * 
 * This class is used to keep track of multiple related web services. It 
 * is used to group related services. For example, if we have create, read, write and 
 * delete services, they can be added to one instance of this class.
 * 
 * When a request is made to the services set, An instance of the class must be created 
 * and the method <a href="#process">WebServicesManager::process()</a> must be called.
 * 
 * @version 1.4.8
 */
class WebServicesManager implements JsonI {
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type error
     */
    const E = 'error';
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type info
     */
    const I = 'info';
    /**
     * An array that contains the supported 'POST' request content types.
     * 
     * This array has the following values:
     * <ul>
     * <li>application/x-www-form-urlencoded</li>
     * <li>multipart/form-data</li>
     * <li>application/json</li>
     * </ul>
     * 
     * @var array An array that contains the supported 'POST' and 'PUT' request content types.
     * 
     * @since 1.1
     */
    const POST_CONTENT_TYPES = [
        'application/x-www-form-urlencoded',
        'multipart/form-data',
        'application/json'
    ];
    /**
     * A general description for the API.
     * 
     * @var string
     * 
     * @since 1.3 
     */
    private $apiDesc;
    /**
     * The version number of the API.
     * 
     * @var string
     * 
     * @since 1.0 
     */
    private $apiVersion;
    /**
     * The filter used to sanitize request parameters.
     * 
     * @var APIFilter
     * 
     * @since 1.0 
     */
    private $filter;
    /**
     * An array which contains body parameters that have invalid values.
     * 
     * @var array
     * 
     * @since 1.4.1 
     */
    private $invParamsArr;
    /**
     * An array which contains the missing required body parameters.
     * 
     * @var array
     * 
     * @since 1.4.1 
     */
    private $missingParamsArr;
    /**
     * The stream at which the output will be sent to.
     * 
     * @var resource|null
     */
    private $outputStream;
    /**
     * The path of the stream.
     * 
     * @var string|null 
     */
    private $outputStreamPath;
    /**
     * An array that contains the web services that can be performed by the API.
     * 
     * @var array
     * 
     * @since 1.0 
     */
    private $services;
    /**
     * Creates new instance of the class.
     * 
     * By default, the API will have two services added to it:
     * <ul>
     * <li>api-info</li>
     * <li>request-info</li>
     * </ul>
     * The first service is used to return a JSON string which contains 
     * all needed information by the front-end to implement the API. The user 
     * can supply an optional parameter with it which is called 'version' in 
     * order to get information about specific API version. The 
     * second service is used to get basic info about the request.
     * 
     * @param string $version initial API version. Default is '1.0.0' Version 
     * number must follow the format 'X.X.X' where 'X' is a number between 
     * 0 and 9 inclusive.
     */
    public function __construct($version = '1.0.0') {
        $this->setVersion($version);
        $this->setDescription('NO DESCRIPTION');

        $this->filter = new APIFilter();
        $this->services = [];
        $this->invParamsArr = [];
        $this->missingParamsArr = [];
    }
    /**
     * Adds new web service to the set of web services.
     * 
     * @param AbstractWebService $service The web service that will be added.
     * 
     * 
     * @since 1.0
     */
    public function addService(AbstractWebService $service) {
        $this->addAction($service);
    }
    /**
     * Sends a response message to indicate that request content type is 
     * not supported by the API.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Content type not supported.",<br/>
     * &nbsp;&nbsp;"type":"error",<br/>
     * &nbsp;&nbsp;"request-content-type":"content_type"<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 404 - Not Found.
     * 
     * @param string $cType The value of the header 'content-type' taken from 
     * request header.
     * 
     * @since 1.1
     */
    public function contentTypeNotSupported($cType = '') {
        $j = new Json();
        $j->add('request-content-type', $cType);
        $this->sendResponse('Content type not supported.', self::E, 404,$j);
    }
    /**
     * Sends a response message to indicate that a database error has occur.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Database Error",<br/>
     * &nbsp;&nbsp;"type":"error",<br/>
     * &nbsp;&nbsp;"err-info":OTHER_DATA<br/>
     * }
     * </p>
     * In here, 'OTHER_DATA' can be a basic string or JSON string.
     * Also, The response will sent HTTP code 404 - Not Found.
     * 
     * @param JsonI|Json|string $info An object of type JsonI or 
     * Json that describe the error in more details. Also it can be a simple string 
     * or JSON string.
     * 
     * @since 1.0
     */
    public function databaseErr($info = '') {
        $this->sendResponse('Database Error.', self::E, 500, $info);
    }
    /**
     * Returns the name of the service which is being called.
     * 
     * The name of the service  must be passed in the body of the request for POST and PUT 
     * request methods (e.g. 'action=do-something' or 'service-name=do-something'). 
     * In case of GET and DELETE, it must be passed as query string. 
     * 
     * @return string|null The name of the service that was requested. If the name 
     * of the service is not set, the method will return null. 
     * 
     * @since 1.0 
     * 
     * @return type
     */
    public function getCalledServiceName() {
        return $this->getAction();
    }
    /**
     * Returns the description of web services set.
     * 
     * @return string|null The description of web services set. The description is 
     * useful to describe what does the set of services can do. If the description is 
     * not set, the method will return null.
     * 
     * @since 1.3
     */
    public function getDescription() {
        return $this->apiDesc;
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
     * be applied. Also, parameters in this case don't apply.s
     * 
     * @return array|Json An array of filtered request inputs. This also can 
     * be an object of type 'Json' if request content type was 'application/json'.
     * 
     * @since 1.0
     */
    public function getInputs() {
        return $this->filter->getInputs();
    }
    /**
     * Returns an array that contains the names of request parameters which have invalid values.
     * 
     * @return array An array that contains the names of request parameters which have invalid values.
     * 
     * @since 1.4.1
     */
    public function getInvalidParameters() {
        return $this->invParamsArr;
    }
    /**
     * Returns an array that contains the names of missing required parameters. 
     * If a parameter is optional and not provided, it will not appear in the returned array.
     * 
     * @return array An array that contains the names of missing required parameters.
     * 
     * @since 1.4.1
     */
    public function getMissingParameters() {
        return $this->missingParamsArr;
    }
    /**
     * Returns an associative array of non-filtered request inputs.
     * The indices of the array will represent request parameters and the 
     * values of each index will represent the value which was set in 
     * request body. The values will be exactly the same as 
     * the values passed in request body. Note that if a parameter is optional and not 
     * provided in request body, its value will be set to 'null'.
     * 
     * @return array An array of request parameters.
     * 
     * @since 1.4.3
     */
    public function getNonFiltered() {
        return $this->filter->getNonFiltered();
    }
    /**
     * Returns the stream at which the output will be sent to.
     * 
     * @return resource|null If a custom output stream is set using the 
     * method 'WebServicesManager::setOutputStream()', the method will return a 
     * resource. The resource will be still open. If no custom stream is set, 
     * the method will return null.
     * 
     * @since 1.4.7
     */
    public function getOutputStream() {
        return $this->outputStream;
    }
    /**
     * Returns a string that represents the path of the custom output stream.
     * 
     * @return string|null A string that represents the path of the custom output stream. 
     * If no custom output stream is set, the method will return null.
     * 
     * @since 1.4.7
     */
    public function getOutputStreamPath() {
        return $this->outputStreamPath;
    }
    /**
     * Returns a web service given its name.
     * 
     * @param string $serviceName The name of the service.
     * 
     * @return AbstractWebService|null The method will return an object of type 'WebService' 
     * if the service is found. If no service was found which has the given name, 
     * The method will return null.
     * 
     * @since 1.3
     */
    public function getServiceByName($serviceName) {
        $trimmed = trim($serviceName);

        if (isset($this->services[$trimmed])) {
            return $this->services[$trimmed];
        }

        return null;
    }
    /**
     * Returns an array that contains all added web services.
     * 
     * @return array An associative array that contains web services as objects. 
     * The indices of the array are services names and the values are objects 
     * of type 'WebService'.
     * 
     * @since 1.0
     */
    public final function getServices() {
        return $this->services;
    }
    /**
     * Returns version number of web services set.
     * 
     * @return string A string in the format 'X.X.X'.
     * 
     * @since 1.0
     */
    public final function getVersion() {
        return $this->apiVersion;
    }
    /**
     * Sends a response message to indicate that a request parameter(s) have invalid values.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"The following parameter(s) has invalid values: 'param_1', 'param_2', 'param_n'",<br/>
     * &nbsp;&nbsp;"type":"error"<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 404 - Not Found.
     * 
     * @since 1.3
     */
    public function invParams() {
        $val = '';
        $i = 0;
        $paramsNamesArr = $this->getInvalidParameters();
        $count = count($paramsNamesArr);

        foreach ($paramsNamesArr as $paramName) {
            if ($i + 1 == $count) {
                $val .= '\''.$paramName.'\'';
            } else {
                $val .= '\''.$paramName.'\', ';
            }
            $i++;
        }
        $this->sendResponse('The following parameter(s) has invalid values: '.$val.'.', self::E, 404);
    }
    /**
     * Checks if request content type is supported by the service or not (For 'POST' 
     * and PUT requests only).
     * 
     * @return boolean Returns false in case the 'content-type' header is not 
     * set and the request method is 'POST' or 'PUT'. If content type is supported (for 
     * PUT and POST), the method will return true, false if not. Other than that, the method 
     * will return true.
     * 
     * @since 1.1
     */
    public final function isContentTypeSupported() {
        $c = Request::getContentType();
        $rm = Request::getMethod();

        if ($c !== null && ($rm == 'POST' || $rm == 'PUT')) {
            return in_array($c, self::POST_CONTENT_TYPES);
        } else if ($c === null && ($rm == 'POST' || $rm == 'PUT')) {
            return false;
        }

        return true;
    }
    /**
     * Sends a response message to indicate that a request parameter or parameters are missing.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"The following required parameter(s) where missing from the request body: 'param_1', 'param_2', 'param_n'",<br/>
     * &nbsp;&nbsp;"type":"error",<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 404 - Not Found.
     * 
     * @since 1.3
     */
    public function missingParams() {
        $val = '';
        $paramsNamesArr = $this->getMissingParameters();
        $i = 0;
        $count = count($paramsNamesArr);

        foreach ($paramsNamesArr as $paramName) {
            if ($i + 1 == $count) {
                $val .= '\''.$paramName.'\'';
            } else {
                $val .= '\''.$paramName.'\', ';
            }
            $i++;
        }
        $this->sendResponse('The following required parameter(s) where missing from the request body: '.$val.'.', self::E, 404);
    }
    /**
     * Sends a response message to tell the front-end that the parameter 
     * 'action', 'service' or 'service-name' is missing from request body.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Service name is not set.",<br/>
     * &nbsp;&nbsp;"type":"error"<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 404 - Not Found.
     * 
     * @since 1.3.1
     */
    public function missingServiceName() {
        $this->sendResponse('Service name is not set.', self::E, 404);
    }
    /**
     * Sends a response message to indicate that a user is not authorized call a 
     * service or a resource.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Not authorized",<br/>
     * &nbsp;&nbsp;"type":"error"<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 401 - Not Authorized.
     * 
     * @since 1.0
     */
    public function notAuth() {
        $this->sendResponse('Not authorized.', self::E, 401);
    }
    /**
     * Process user request. 
     * 
     * This method must be called after creating any 
     * new instance of the class in order to process user request.
     * 
     * @since 1.0
     */
    public final function process() {
        $this->invParamsArr = [];
        $this->missingParamsArr = [];

        if ($this->isContentTypeSupported()) {
            if ($this->_checkAction()) {
                $actionObj = $this->getServiceByName($this->getCalledServiceName());
                $params = $actionObj->getParameters();
                $this->filter->clearParametersDef();
                $this->filter->clearInputs();

                foreach ($params as $param) {
                    $this->filter->addRequestParameter($param);
                }
                $this->_filterInputs();
                $i = $this->getInputs();

                if (!($i instanceof Json)) {
                    $this->_processNonJson($params);
                } else {
                    $this->_processJson($params);
                }
            }
        } else {
            $this->contentTypeNotSupported(Request::getContentType());
        }
    }
    /**
     * Reads the content of output stream.
     * 
     * This method is used to read the content of the custom output stream. The 
     * method will only read it if it was set using its path.
     * 
     * @return string|null If the content was taken from the stream, the method 
     * will return it as a string. Other than that, the method will return null.
     * 
     * @since 1.4.7
     */
    public function readOutputStream() {
        $path = $this->getOutputStreamPath();

        if ($path !== null) {
            return file_get_contents($path);
        }
    }
    /**
     * Removes a service from the manager given its name.
     * 
     * @param string $name The name of the service.
     * 
     * @return AbstractWebService|null If a web service which has the given name was found 
     * and removed, the method will return an object that represent the removed 
     * service. Other than that, the method will return null.
     * 
     * @since 1.4.8
     */
    public function removeService($name) {
        $trimmed = trim($name);
        $service = $this->getServiceByName($trimmed);

        if ($service !== null) {
            $service->setManager(null);
            unset($this->services[$trimmed]);
        }

        return $service;
    }
    /**
     * Removes all added web services.
     * 
     * This method will simply re-initialize the arrays that holds all web 
     * services.
     * 
     * @since 1.4.5
     */
    public function removeServices() {
        $this->services = [];
    }
    /**
     * Sends a response message to indicate that request method is not supported.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Method Not Allowed.",<br/>
     * &nbsp;&nbsp;"type":"error",<br/>
     * }
     * </p>
     * 
     * In addition to the message, The response will sent HTTP code 405 - Method Not Allowed.
     * 
     * @since 1.0
     */
    public function requestMethodNotAllowed() {
        $this->sendResponse('Method Not Allowed.', self::E, 405);
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
     */
    public function send($conentType,$data,$code = 200) {
        if ($this->getOutputStream() !== null) {
            fwrite($this->getOutputStream(), $data.'');
            fclose($this->getOutputStream());
        } else {
            Response::addHeader('content-type', $conentType);
            Response::write($data);
            Response::setCode($code);
            Response::send();
        }
    }
    /**
     * Sends back multiple HTTP headers to the client.
     * 
     * @param array $headersArr An associative array. The keys will act as 
     * the headers names and the value of each key will represents the value 
     * of the header.
     * 
     * @since 1.4.3
     */
    public function sendHeaders(array $headersArr) {
        foreach ($headersArr as $header => $val) {
            Response::addHeader($header, $val);
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
     * @since 1.0
     */
    public function sendResponse($message,$type = '',$code = 200,$otherInfo = null) {
        $json = new Json();
        $json->add('message', $message);
        $typeTrimmed = trim($type);

        if (strlen($typeTrimmed) !== 0) {
            $json->add('type', $typeTrimmed);
        }
        $json->add('http-code', $code);

        if ($otherInfo !== null) {
            $json->add('more-info', $otherInfo);
        }

        if ($this->getOutputStream() !== null) {
            fwrite($this->getOutputStream(), $json);
            fclose($this->getOutputStream());
        } else {
            Response::addHeader('content-type', 'application/json');
            Response::write($json);
            Response::setCode($code);
            Response::send();
        }
    }
    /**
     * Sends a response message to indicate that web service is not implemented.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Service not implemented.",<br/>
     * &nbsp;&nbsp;"type":"error",<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 404 - Not Found.
     * 
     * @since 1.0
     */
    public function serviceNotImplemented() {
        $this->sendResponse('Service not implemented.', self::E, 404);
    }
    /**
     * Sends a response message to indicate that called web service is not supported by the API.
     * 
     * This method will send back a JSON string in the following format:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Action not supported",<br/>
     * &nbsp;&nbsp;"type":"error"<br/>
     * }
     * </p>
     * In addition to the message, The response will sent HTTP code 404 - Not Found.
     * 
     * @since 1.0
     */
    public function serviceNotSupported() {
        $this->sendResponse('Service not supported.', self::E, 404);
    }
    /**
     * Sets the description of the web services set.
     * 
     * @param sting $desc Set description. Used to help front-end to identify 
     * the use of the services set.
     * 
     * @since 1.3
     */
    public function setDescription($desc) {
        $this->apiDesc = $desc;
    }
    /**
     * Sets the stream at which the manager will read the inputs from.
     * 
     * This can be used to test the services if body content type is 
     * 'application/json'.
     * 
     * @param string|resource $pathOrResource A file that contains JSON or 
     * a stream which was opened using a function like 'fopen()'.
     * 
     * @return boolean If input stream is successfully set, the method will 
     * return true. False otherwise.
     * 
     * @since 1.4.8
     */
    public function setInputStream($pathOrResource) {
        return $this->filter->setInputStream($pathOrResource);
    }
    /**
     * Sets a custom output stream.
     * 
     * This method is useful if the developer would like to test the output of a 
     * web service. Simply set the output stream to a custom one and read the 
     * content of the stream. Note that  if the 
     * resource already exist and has content, it will be erased.
     * 
     * @param resource|string $stream A resource which was opened by 'fopen()'. Also, 
     * it can be a string that points to a file.
     * 
     * @param boolean $new If set to true and the resource does not exist, the 
     * method will attempt to create it.
     * 
     * @since 1.4.7
     */
    public function setOutputStream($stream, $new = false) {
        if (is_resource($stream)) {
            $this->outputStream = $stream;
            $meat = stream_get_meta_data($this->outputStream);
            $this->outputStreamPath = $meat['uri'];
            file_put_contents($this->getOutputStreamPath(),'');

            return true;
        } 

        $trimmed = trim($stream);

        if (strlen($trimmed) > 0) {
            $create = $new === true;

            if (is_file($stream)) {
                return $this->setOutputStreamHelper($trimmed, 'r+');
            } else if ($create) {
                return $this->setOutputStreamHelper($trimmed, 'w');
            }
        }

        return false;
    }
    /**
     * Sets version number of the set.
     * 
     * @param string $val Version number (such as 1.0.0). Version number 
     * must be provided in the form 'x.x.x' where 'x' is a number between 
     * 0 and 9 inclusive.
     * 
     * @return boolean true if set. false otherwise.
     * 
     * @since 1.0
     */
    public final function setVersion($val) {
        $nums = explode('.', $val);

        if (count($nums) == 3) {
            foreach ($nums as $v) {
                $len = strlen($v);

                for ($x = 0 ; $x < $len ; $x++) {
                    if ($v[$x] < '0' || $v[$x] > '9') {
                        return false;
                    }
                }
            }
            $this->apiVersion = $val;

            return true;
        }

        return false;
    }
    /**
     * Returns Json object that represents services set.
     * 
     * @return Json An object of type Json.
     * 
     * @since 1.0
     */
    public function toJSON() {
        $json = new Json();
        $json->add('api-version', $this->getVersion());
        $json->add('description', $this->getDescription());
        $i = $this->getInputs();

        if ($i instanceof Json) {
            $vNum = $i->get('version');
        } else {
            $vNum = isset($i['version']) ? $i['version'] : null;
        }

        if ($vNum === null || $vNum === false) {
            $json->add('services', $this->getServices());
        } else {
            $actionsArr = [];

            foreach ($this->getServices() as $a) {
                if ($a->getSince() == $vNum) {
                    array_push($actionsArr, $a);
                }
            }
            $json->add('services', $actionsArr);
        }

        return $json;
    }
    private function _AfterParamsCheck($processReq) {
        if ($processReq) {
            $service = $this->getServiceByName($this->getCalledServiceName());
            $isAuth = !$service->isAuthRequred() || $service->isAuthorized() === null || $service->isAuthorized();

            if ($isAuth) {
                $service->processRequest();
            } else {
                $this->notAuth();
            }
        } else if (count($this->missingParamsArr) != 0) {
            $this->missingParams();
        } else if (count($this->invParamsArr) != 0) {
            $this->invParams();
        }
    }
    /**
     * Checks the status of the called service.
     * 
     * This method checks if the following conditions are met:
     * <ul>
     * <li>The parameter "action", "service-name" or "service" is set in request body.</li>
     * <li>The service is supported by the set.</li>
     * <li>Request method of the service is correct.</li>
     * </ul>
     * If one of the conditions is not met, the method will return false and 
     * send back a response to indicate the issue.
     * 
     * @return boolean true if called service is valid.
     * 
     * @since 1.0
     */
    private function _checkAction() {
        $serviceName = $this->getCalledServiceName();
        //first, check if action is set and not null
        if ($serviceName !== null) {
            $calledService = $this->getServiceByName($serviceName);
            //after that, check if action is supported by the API.
            if ($calledService !== null) {
                $allowedMethods = $calledService->getRequestMethods();

                if (count($allowedMethods) != 0) {
                    $isValidRequestMethod = in_array(Request::getMethod(), $allowedMethods);

                    if (!$isValidRequestMethod) {
                        $this->requestMethodNotAllowed();
                    } else {
                        return true;
                    }
                } else {
                    $this->sendResponse('Request methods of the service are not set in code.', self::E, 404);
                }
            } else {
                $this->serviceNotSupported();
            }
        } else {
            $this->missingServiceName();
        }

        return false;
    }
    private function _filterInputs() {
        $reqMeth = Request::getMethod();
        $contentType = Request::getContentType();

        if ($reqMeth == 'GET' || 
            $reqMeth == 'DELETE' || 
            ($reqMeth == 'PUT' && $contentType != 'application/json') || 
            $reqMeth == 'OPTIONS' || 
            $reqMeth == 'PATCH') {
            $this->filter->filterGET();
        } else if ($reqMeth == 'POST' || ($reqMeth == 'PUT' && $contentType == 'application/json')) {
            $this->filter->filterPOST();
        }
    }
    private function _processJson($params) {
        $processReq = true;
        $i = $this->getInputs();
        $paramsNames = $i->getPropsNames();

        foreach ($params as $param) {
            if (!$param->isOptional() && !in_array($param->getName(), $paramsNames)) {
                array_push($this->missingParamsArr, $param->getName());
                $processReq = false;
            }

            if ($i->get($param->getName()) === null) {
                array_push($this->invParamsArr, $param->getName());
                $processReq = false;
            }
        }
        $this->_AfterParamsCheck($processReq);
    }
    private function _processNonJson($params) {
        $processReq = true;
        $i = $this->getInputs();

        foreach ($params as $param) {
            if (!$param->isOptional() && !isset($i[$param->getName()])) {
                array_push($this->missingParamsArr, $param->getName());
                $processReq = false;
            }

            if (isset($i[$param->getName()]) && $i[$param->getName()] === 'INV') {
                array_push($this->invParamsArr, $param->getName());
                $processReq = false;
            }
        }
        $this->_AfterParamsCheck($processReq);
    }
    /**
     * Adds new web service to the set of web services.
     * 
     * @param AbstractWebService $service The web service that will be added.
     * 
     * @since 1.0
     * 
     * @deprecated since version 1.4.7 Use WebservicesSet::addService()
     */
    private function addAction(AbstractWebService $service) {
        $this->services[$service->getName()] = $service;
        $service->setManager($this);
    }
    /**
     * Returns the name of the service which is being called.
     * 
     * The name of the service  must be passed in the body of the request for POST and PUT 
     * request methods (e.g. 'action=do-something' or 'service=do-something'). 
     * In case of GET and DELETE, it must be passed as query string. 
     * 
     * @return string|null The name of the service that was requested. If the name 
     * of the service is not set, the method will return null. 
     * 
     * @since 1.0
     * 
     * @deprecated since version 1.4.6 Use WebServicesManager::getCalledServiceName() instead.
     */
    private function getAction() {
        $reqMeth = Request::getMethod();

        $serviceIdx = ['action','service', 'service-name'];

        $contentType = Request::getContentType();
        $retVal = null;

        if ($contentType == 'application/json') {
            $inputsPath = $this->filter->getInputStreamPath();
            $streamPath = $inputsPath !== null ? $inputsPath : 'php://input';
            $body = file_get_contents($streamPath);
            $jsonx = json_decode($body, true);

            if (gettype($jsonx) == 'array') {
                foreach ($serviceIdx as $index) {
                    if (isset($jsonx[$index])) {
                        $retVal = filter_var($jsonx[$index]);
                        break;
                    }
                }
            }

            return $retVal;
        }

        foreach ($serviceIdx as $serviceNameIndex) {
            if (($reqMeth == 'GET' || 
               $reqMeth == 'DELETE' ||  
               $reqMeth == 'OPTIONS' || 
               $reqMeth == 'PATCH') && isset($_GET[$serviceNameIndex])) {
                $retVal = filter_var($_GET[$serviceNameIndex]);
            } else if (($reqMeth == 'POST' || $reqMeth == 'PUT') && isset($_POST[$serviceNameIndex])) {
                $retVal = filter_var($_POST[$serviceNameIndex]);
            }
        }

        return $retVal;
    }

    private function setOutputStreamHelper($trimmed, $mode) {
        $tempStream = fopen($trimmed, $mode);

        if (is_resource($tempStream)) {
            $this->outputStream = $tempStream;
            $this->outputStreamPath = $trimmed;
            file_put_contents($this->getOutputStreamPath(),'');

            return true;
        }

        return false;
    }
}
