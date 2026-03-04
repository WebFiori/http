<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use Exception;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;
/**
 * A class that is used to manage multiple web services.
 * 
 * This class is used to keep track of multiple related web services. It 
 * is used to group related services. For example, if we have creat, read, write and
 * delete services, they can be added to one instance of this class.
 * 
 * When a request is made to the services set, An instance of the class must be created 
 * and the method <a href="#process">WebServicesManager::process()</a> must be called.
 * 
 */
class WebServicesManager implements JsonI {
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
     */
    private $apiDesc;
    /**
     * The version number of the API.
     * 
     * @var string
     * 
     */
    private $apiVersion;
    /**
     * The base path for all services in this manager.
     * 
     * @var string
     */
    private string $basePath = '';
    /**
     * The filter used to sanitize request parameters.
     * 
     * @var APIFilter
     * 
     */
    private $filter;
    /**
     * An array which contains body parameters that have invalid values.
     * 
     * @var array
     * 
     */
    private $invParamsArr;
    /**
     * An array which contains the missing required body parameters.
     * 
     * @var array
     * 
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
    private $request;
    /**
     * The response object used to send output.
     * 
     * @var Response
     */
    private $response;
    /**
     * An array that contains the web services that can be performed by the API.
     * 
     * @var array
     * 
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
    public function __construct(?Request $request = null, string $version = '1.0.0') {
        $this->setVersion($version);
        $this->setDescription('NO DESCRIPTION');

        $this->filter = new APIFilter();
        $this->services = [];
        $this->invParamsArr = [];
        $this->missingParamsArr = [];
        $this->request = $request ?? Request::createFromGlobals();
        $this->response = new Response();
    }
    /**
     * Adds new web service to the set of web services.
     * 
     * @param WebService $service The web service that will be added.
     * 
     * 
     */
    public function addService(WebService $service) : WebServicesManager {
        return $this->addAction($service);
    }
    /**
     * Automatically discovers and registers web services from a directory.
     * 
     * @param string $path The directory path to scan for service classes. Defaults to current directory.
     * 
     * @return WebServicesManager Returns the same instance for method chaining.
     */
    public function autoDiscoverServices(?string $path = null) : WebServicesManager {
        if ($path === null) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            $path = dirname($trace[0]['file']);
        }

        $files = glob($path.'/*.php');
        $beforeClasses = get_declared_classes();

        foreach ($files as $file) {
            require_once $file;
        }

        $afterClasses = get_declared_classes();
        $newClasses = array_diff($afterClasses, $beforeClasses);

        foreach ($newClasses as $class) {
            if (is_subclass_of($class, WebService::class)) {
                $reflection = new \ReflectionClass($class);

                if (!$reflection->isAbstract()) {
                    $this->addService(new $class());
                }
            }
        }

        return $this;
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
     * In addition to the message, The response will send HTTP code 404 - Not Found.
     * 
     * @param string $cType The value of the header 'content-type' taken from 
     * request header.
     * 
     */
    public function contentTypeNotSupported(string $cType = '') {
        $j = new Json();
        $j->add('request-content-type', $cType);
        $this->sendResponse(ResponseMessage::get('415'), 415, WebService::E, $j);
    }
    /**
     * Returns the base path for all services.
     * 
     * @return string The base path.
     */
    public function getBasePath(): string {
        return $this->basePath;
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
     *
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
     */
    public function getInputs() {
        return $this->filter->getInputs();
    }
    /**
     * Returns an array that contains the names of request parameters which have invalid values.
     * 
     * @return array An array that contains the names of request parameters which have invalid values.
     * 
     */
    public function getInvalidParameters() : array {
        return $this->invParamsArr;
    }
    /**
     * Returns an array that contains the names of missing required parameters. 
     * If a parameter is optional and not provided, it will not appear in the returned array.
     * 
     * @return array An array that contains the names of missing required parameters.
     * 
     */
    public function getMissingParameters() : array {
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
     */
    public function getNonFiltered() : array {
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
     */
    public function getOutputStreamPath() {
        return $this->outputStreamPath;
    }
    public function getRequest() : Request {
        return $this->request;
    }
    /**
     * Returns the response object used by the manager.
     * 
     * @return Response
     */
    public function getResponse() : Response {
        return $this->response;
    }
    /**
     * Returns a web service given its name.
     * 
     * @param string $serviceName The name of the service.
     * 
     * @return WebService|null The method will return an object of type 'WebService' 
     * if the service is found. If no service was found which has the given name, 
     * The method will return null.
     * 
     */
    public function getServiceByName(string $serviceName) {
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
     */
    public final function getServices() : array {
        return $this->services;
    }
    /**
     * Returns version number of web services set.
     * 
     * @return string A string in the format 'X.X.X'.
     * 
     */
    public final function getVersion() : string {
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
     * In addition to the message, The response will send HTTP code 404 - Not Found.
     * 
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
        $this->sendResponse(ResponseMessage::get('404-1').$val.'.', 404, WebService::E, new Json([
            'invalid' => $paramsNamesArr
        ]));
    }
    /**
     * Checks if request content type is supported by the service or not (For 'POST' 
     * and PUT requests only).
     * 
     * @return bool Returns false in case the 'content-type' header is not 
     * set and the request method is 'POST' or 'PUT'. If content type is supported (for 
     * PUT and POST), the method will return true, false if not. Other than that, the method 
     * will return true.
     * 
     */
    public final function isContentTypeSupported() : bool {
        $c = $this->getRequest()->getContentType();
        $rm = $this->getRequest()->getMethod();

        if ($c !== null && ($rm == RequestMethod::POST || $rm == RequestMethod::PUT)) {
            // Check if content type starts with any of the supported types
            foreach (self::POST_CONTENT_TYPES as $supportedType) {
                if (strpos($c, $supportedType) === 0) {
                    return true;
                }
            }

            return false;
        } else if ($c === null && ($rm == RequestMethod::POST || $rm == RequestMethod::PUT)) {
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
     * In addition to the message, The response will send HTTP code 404 - Not Found.
     * 
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
        $this->sendResponse(ResponseMessage::get('404-2').$val.'.', 404, WebService::E, new Json([
            'missing' => $paramsNamesArr
        ]));
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
     * In addition to the message, The response will send HTTP code 404 - Not Found.
     * 
     */
    public function missingServiceName() {
        $this->sendResponse(ResponseMessage::get('404-3'), 404, WebService::E);
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
     * In addition to the message, The response will send HTTP code 401 - Not Authorized.
     * 
     */
    public function notAuth() {
        $this->sendResponse(ResponseMessage::get('401'), 401, WebService::E);
    }

    /**
     * Process user request.
     *
     * This method must be called after creating any
     * new instance of the class in order to process user request.
     *
     * @throws Exception
     */
    public final function process() {
        $this->invParamsArr = [];
        $this->missingParamsArr = [];

        if ($this->isContentTypeSupported()) {
            if ($this->_checkAction()) {
                $actionObj = $this->getServiceByName($this->getCalledServiceName());

                // Configure parameters for ResponseBody services before getting them
                if ($this->serviceHasResponseBodyMethods($actionObj)) {
                    $this->configureServiceParameters($actionObj);
                }

                $params = $actionObj->getParameters();
                $params = $actionObj->getParameters();
                $this->filter->clearParametersDef();
                $this->filter->clearInputs();
                $requestMethod = $this->getRequest()->getRequestMethod();

                foreach ($params as $param) {
                    $paramMethods = $param->getMethods();

                    if (count($paramMethods) == 0 || in_array($requestMethod, $paramMethods)) {
                        $this->filter->addRequestParameter($param);
                    }
                }
                $this->filterInputsHelper();
                $i = $this->getInputs();

                if (!($i instanceof Json)) {
                    $this->_processNonJson($this->filter->getParameters());
                } else {
                    $this->_processJson($this->filter->getParameters());
                }
            }
        } else {
            $c = $this->getRequest()->getContentType();

            if ($c === null) {
                $c = 'NOT_SET';
            } 
            $this->contentTypeNotSupported($c);
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
     */
    public function readOutputStream() {
        $path = $this->getOutputStreamPath();

        if ($path !== null) {
            return file_get_contents($path);
        }

        return null;
    }
    /**
     * Removes a service from the manager given its name.
     * 
     * @param string $name The name of the service.
     * 
     * @return WebService|null If a web service which has the given name was found 
     * and removed, the method will return an object that represent the removed 
     * service. Other than that, the method will return null.
     * 
     */
    public function removeService(string $name) {
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
     * In addition to the message, The response will send HTTP code 405 - Method Not Allowed.
     * 
     */
    public function requestMethodNotAllowed() {
        $this->sendResponse(ResponseMessage::get('405'), 405, WebService::E);
    }
    /**
     * Sends Back a data using specific content type and specific response code.
     * 
     * @param string $contentType Response content type (such as 'application/json')
     * 
     * @param mixed $data Any data to send back. Mostly, it will be a string.
     * 
     * @param int $code HTTP response code that will be used to send the data. 
     * Default is HTTP code 200 - Ok.
     */
    public function send(string $contentType, $data, int $code = 200) {
        if ($this->getOutputStream() !== null) {
            fwrite($this->getOutputStream(), $data.'');
            fclose($this->getOutputStream());
        } else {
            $this->response->addHeader('content-type', $contentType);
            $this->response->write($data);
            $this->response->setCode($code);
            $this->response->send();
        }
    }
    /**
     * Sends back multiple HTTP headers to the client.
     * 
     * @param array $headersArr An associative array. The keys will act as headers names,
     * and the value of each key will represent the value
     * of the header.
     * 
     */
    public function sendHeaders(array $headersArr) {
        foreach ($headersArr as $header => $val) {
            $this->response->addHeader($header, $val, null);
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
     */
    public function sendResponse(string $message, int $code = 200, string $type = '', mixed $otherInfo = '') {
        $json = new Json();
        $json->add('message', $message);
        $typeTrimmed = trim($type);

        if (strlen($typeTrimmed) !== 0) {
            $json->add('type', $typeTrimmed);
        }
        $json->add('http-code', $code);

        if ($otherInfo !== null) {
            if (gettype($otherInfo) != 'string' || strlen($otherInfo) != 0) {
                $json->add('more-info', $otherInfo);
            }
        }

        if ($this->getOutputStream() !== null) {
            fwrite($this->getOutputStream(), $json);
            fclose($this->getOutputStream());
        } else {
            $this->response->addHeader('content-type', 'application/json');
            $this->response->write($json);
            $this->response->setCode($code);
            $this->response->send();
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
     * In addition to the message, The response will send HTTP code 404 - Not Found.
     * 
     */
    public function serviceNotImplemented() {
        $this->sendResponse(ResponseMessage::get('404-4'), 404, WebService::E);
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
     * In addition to the message, The response will send HTTP code 404 - Not Found.
     * 
     */
    public function serviceNotSupported() {
        $this->sendResponse(ResponseMessage::get('404-5'), 404, WebService::E);
    }
    /**
     * Sets the base path for all services in this manager.
     * 
     * The base path will be prepended to each service name when generating paths.
     * For example, if base path is "/api/v1" and service name is "user",
     * the final path will be "/api/v1/user".
     * 
     * @param string $basePath The base path (e.g., "/api/v1"). Leading/trailing slashes are handled automatically.
     * 
     * @return WebServicesManager Returns self for method chaining.
     */
    public function setBasePath(string $basePath): WebServicesManager {
        $this->basePath = rtrim($basePath, '/');

        return $this;
    }
    /**
     * Sets the description of the web services set.
     * 
     * @param string $desc Set description. Used to help front-end to identify
     * the use of the services set.
     * 
     */
    public function setDescription(string $desc) {
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
     * @return bool If input stream is successfully set, the method will 
     * return true. False otherwise.
     * 
     */
    public function setInputStream($pathOrResource) : bool {
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
     * @param bool $new If set to true and the resource does not exist, the 
     * method will attempt to create it.
     * 
     */
    public function setOutputStream($stream, bool $new = false): bool {
        if (is_resource($stream)) {
            $this->outputStream = $stream;
            $meat = stream_get_meta_data($this->outputStream);
            $this->outputStreamPath = $meat['uri'];
            file_put_contents($this->getOutputStreamPath(),'');

            return true;
        } 

        $trimmed = trim((string)$stream);

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
    public function setRequest(Request $request) : WebServicesManager {
        $this->request = $request;

        return $this;
    }
    /**
     * Sets version number of the set.
     * 
     * @param string $val Version number (such as 1.0.0). Version number 
     * must be provided in the form 'x.x.x' where 'x' is a number between 
     * 0 and 9 inclusive.
     * 
     * @return bool true if set. false otherwise.
     * 
     */
    public final function setVersion(string $val) : bool {
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
     */
    public function toJSON() : Json {
        $json = new Json();
        $json->add('api-version', $this->getVersion());
        $json->add('description', $this->getDescription());
        $i = $this->getInputs();

        if ($i instanceof Json) {
            $vNum = $i->get('version');
        } else {
            $vNum = $i['version'] ?? null;
        }

        if ($vNum === null || $vNum === false) {
            $json->add('services', $this->getServices());
        } else {
            $actionsArr = [];

            foreach ($this->getServices() as $a) {
                if ($a->getSince() == $vNum) {
                    $actionsArr[] = $a;
                }
            }
            $json->add('services', $actionsArr);
        }

        return $json;
    }
    /**
     * Converts the services manager to an OpenAPI document.
     * 
     * This method generates a complete OpenAPI 3.1.0 specification document
     * from the registered services. Each service becomes a path in the document.
     * 
     * @return OpenAPI\OpenAPIObj The OpenAPI document.
     */
    public function toOpenAPI(): OpenAPI\OpenAPIObj {
        $info = new OpenAPI\InfoObj(
            $this->getDescription(),
            $this->getVersion()
        );

        $openapi = new OpenAPI\OpenAPIObj($info);

        $paths = new OpenAPI\PathsObj();

        foreach ($this->getServices() as $service) {
            $path = $this->basePath.'/'.$service->getName();
            $paths->addPath($path, $service->toPathItemObj());
        }

        $openapi->setPaths($paths);

        return $openapi;
    }
    private function _AfterParamsCheck($processReq) {
        if ($processReq) {
            $service = $this->getServiceByName($this->getCalledServiceName());


            if ($this->isAuth($service)) {
                $this->processService($service);
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
     * @return bool true if called service is valid.
     * 
     */
    private function _checkAction(): bool {
        $serviceName = $this->getCalledServiceName();

        //first, check if action is set and not null
        if ($serviceName !== null) {
            $calledService = $this->getServiceByName($serviceName);

            //after that, check if action is supported by the API.
            if ($calledService !== null) {
                $allowedMethods = $calledService->getRequestMethods();

                if (count($allowedMethods) != 0) {
                    $isValidRequestMethod = in_array($this->getRequest()->getMethod(), $allowedMethods);

                    if (!$isValidRequestMethod) {
                        $this->requestMethodNotAllowed();
                    } else {
                        return true;
                    }
                } else {
                    $this->sendResponse(ResponseMessage::get('404-6'), 404, WebService::E);
                }
            } else {
                $this->serviceNotSupported();
            }
        } else {
            $this->missingServiceName();
        }

        return false;
    }
    private function _processJson($params) {
        $processReq = true;
        $i = $this->getInputs();
        $paramsNames = $i->getPropsNames();

        foreach ($params as $param) {
            if (!$param->isOptional() && !in_array($param->getName(), $paramsNames)) {
                $this->missingParamsArr[] = $param->getName();
                $processReq = false;
            }

            if ($i->get($param->getName()) === null) {
                $this->invParamsArr[] = $param->getName();
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
                $this->missingParamsArr[] = $param->getName();
                $processReq = false;
            }

            if (isset($i[$param->getName()]) && $i[$param->getName()] === 'INV') {
                $this->invParamsArr[] = $param->getName();
                $processReq = false;
            }
        }
        $this->_AfterParamsCheck($processReq);
    }
    /**
     * Adds new web service to the set of web services.
     * 
     * @param WebService $service The web service that will be added.
     * 
     * 
     * @deprecated since version 1.4.7 Use WebservicesSet::addService()
     */
    private function addAction(WebService $service) : WebServicesManager {
        $this->services[$service->getName()] = $service;
        $service->setManager($this);

        return $this;
    }

    /**
     * Configure parameters for the target method of a service.
     */
    private function configureServiceParameters(WebService $service): void {
        if (method_exists($service, 'getTargetMethod')) {
            $targetMethod = $service->getTargetMethod();

            if ($targetMethod && method_exists($service, 'configureParametersForMethod')) {
                $reflection = new \ReflectionMethod($service, 'configureParametersForMethod');
                $reflection->setAccessible(true);
                $reflection->invoke($service, $targetMethod);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function filterInputsHelper() {
        $reqMeth = $this->getRequest()->getMethod();
        $contentType = $this->getRequest()->getContentType();

        if ($reqMeth == RequestMethod::GET || 
            $reqMeth == RequestMethod::DELETE || 
            $reqMeth == RequestMethod::HEAD) {
            $this->filter->filterGET();
        } else if ($reqMeth == RequestMethod::POST || 
                   $reqMeth == RequestMethod::PUT || 
                   $reqMeth == RequestMethod::PATCH) {
            // Populate PUT/PATCH data before filtering
            if ($reqMeth == RequestMethod::PUT || $reqMeth == RequestMethod::PATCH) {
                $this->populatePutData($contentType);
            }
            $this->filter->filterPOST();
        }
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
     * 
     * @deprecated since version 1.4.6 Use WebServicesManager::getCalledServiceName() instead.
     */
    private function getAction() {
        $services = $this->getServices();

        if (count($services) == 1) {
            return $services[array_keys($services)[0]]->getName();
        }
        $reqMeth = $this->getRequest()->getMethod();

        $serviceIdx = ['action','service', 'service-name'];

        $contentType = $this->getRequest()->getContentType();
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
            if (($reqMeth == RequestMethod::GET || 
               $reqMeth == RequestMethod::DELETE ||  
               $reqMeth == RequestMethod::CONNECT || 
               $reqMeth == RequestMethod::HEAD || 
               $reqMeth == RequestMethod::TRACE || 
               $reqMeth == RequestMethod::OPTIONS) && isset($_GET[$serviceNameIndex])) {
                $retVal = filter_var($_GET[$serviceNameIndex]);
            } else if ($reqMeth == RequestMethod::POST && isset($_POST[$serviceNameIndex])) {
                $retVal = filter_var($_POST[$serviceNameIndex]);
            } else if ($reqMeth == RequestMethod::PUT || $reqMeth == RequestMethod::PATCH) {
                $this->populatePutData($contentType);

                if (isset($_POST[$serviceNameIndex])) {
                    $retVal = filter_var($_POST[$serviceNameIndex]);
                }
            }
        }

        return $retVal;
    }
    private function isAuth(WebService $service) {
        $isAuth = false;

        if ($service->isAuthRequired()) {
            // Check if method has authorization annotations
            if ($service->hasMethodAuthorizationAnnotations()) {
                // Use annotation-based authorization
                return $service->checkMethodAuthorization();
            }

            // Fall back to legacy HTTP-method-specific authorization
            $isAuthCheck = 'isAuthorized'.$this->getRequest()->getMethod();

            if (!method_exists($service, $isAuthCheck)) {
                return $service->isAuthorized() === null || $service->isAuthorized();
            }

            return $service->$isAuthCheck() === null || $service->$isAuthCheck();
        }

        return true;
    }
    private function populatePutData(string $contentType) {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $input = file_get_contents('php://input');

        if (empty($input)) {
            return;
        }

        // Handle application/x-www-form-urlencoded
        if (strpos($contentType, 'application/x-www-form-urlencoded') === 0) {
            parse_str($input, $_POST);

            return;
        }

        // Handle multipart/form-data
        if (strpos($contentType, 'multipart/form-data') === 0) {
            // Extract boundary from content type
            preg_match('/boundary=(.+)$/', $contentType, $matches);

            if (!isset($matches[1])) {
                return;
            }

            $boundary = '--'.$matches[1];
            $parts = explode($boundary, $input);

            foreach ($parts as $part) {
                if (trim($part) === '' || trim($part) === '--') {
                    continue;
                }

                // Split headers and content
                $sections = explode("\r\n\r\n", $part, 2);

                if (count($sections) !== 2) {
                    continue;
                }

                $headers = $sections[0];
                $content = rtrim($sections[1], "\r\n");

                // Parse Content-Disposition header
                if (preg_match('/name="([^"]+)"/', $headers, $nameMatch)) {
                    $fieldName = $nameMatch[1];

                    // Check if it's a file upload
                    if (preg_match('/filename="([^"]*)"/', $headers, $fileMatch)) {
                        // Handle file upload
                        $filename = $fileMatch[1];

                        // Extract content type if present
                        $fileType = 'application/octet-stream';

                        if (preg_match('/Content-Type:\s*(.+)/i', $headers, $typeMatch)) {
                            $fileType = trim($typeMatch[1]);
                        }

                        // Create temporary file
                        $tmpFile = tempnam(sys_get_temp_dir(), 'put_upload_');
                        file_put_contents($tmpFile, $content);

                        $_FILES[$fieldName] = [
                            'name' => $filename,
                            'type' => $fileType,
                            'tmp_name' => $tmpFile,
                            'error' => UPLOAD_ERR_OK,
                            'size' => strlen($content)
                        ];
                    } else {
                        // Regular form field
                        $_POST[$fieldName] = $content;
                    }
                }
            }
        }
    }
    private function processService(WebService $service) {
        // Try auto-processing only if service has ResponseBody methods
        if ($this->serviceHasResponseBodyMethods($service)) {
            // Configure parameters for the target method before processing
            $this->configureServiceParameters($service);
            $service->processWithAutoHandling();

            return;
        }

        $processMethod = 'process'.$this->getRequest()->getMethod();

        if (!method_exists($service, $processMethod)) {
            $service->processRequest();
        } else {
            $service->$processMethod();
        }
    }
    /**
     * Check if service has any methods with ResponseBody annotation.
     */
    private function serviceHasResponseBodyMethods(WebService $service): bool {
        $reflection = new \ReflectionClass($service);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(Annotations\ResponseBody::class);

            if (!empty($attributes)) {
                return true;
            }
        }

        return false;
    }
    private function setOutputStreamHelper($trimmed, $mode) : bool {
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
