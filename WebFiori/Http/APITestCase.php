<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use PHPUnit\Framework\TestCase;
use WebFiori\Json\Json;
use WebFiori\Json\JsonException;
/**
 * A helper class which is used to implement test cases for API calls.
 *
 * This class will mimic the process of sending HTTP request to an endpoint and
 * store the output temporarily on a file. At second stage, the developer
 * can read the output and compare it to an expected output.
 * 
 * @author Ibrahim
 */
class APITestCase extends TestCase {
    const NL = "\r\n";
    const DEFAULT_OUTPUT_STREAM = __DIR__.DIRECTORY_SEPARATOR.'output-stream.txt';
    /**
     * The path to the output stream file.
     * 
     * @var string
     */
    private $outputStreamPath;
    /**
     * Backup of global variables.
     * 
     * @var array
     */
    private $backupGlobals;
    
    protected function setUp(): void {
        parent::setUp();
        $this->backupGlobals = [
            'GET' => $_GET,
            'POST' => $_POST,
            'FILES' => $_FILES,
            'SERVER' => $_SERVER
        ];
    }
    
    protected function tearDown(): void {
        $_GET = $this->backupGlobals['GET'];
        $_POST = $this->backupGlobals['POST'];
        $_FILES = $this->backupGlobals['FILES'];
        $_SERVER = $this->backupGlobals['SERVER'];
        parent::tearDown();
    }
    /**
     * Sets the path to the file which is used to store API output temporarily.
     * 
     * @param string $path The absolute path to the file.
     */
    public function setOutputFile(string $path) {
        $this->outputStreamPath = $path;
    }
    /**
     * Returns the path to the file which is used to store API output temporarily.
     * 
     * @return string
     */
    public function getOutputFile() : string {
        if ($this->outputStreamPath === null) {
            $this->outputStreamPath = self::DEFAULT_OUTPUT_STREAM;
        }
        return $this->outputStreamPath;
    }
    /**
     * Adds a file to the array $_FILES for testing API with upload.
     * 
     * @param string $fileIdx The name of the index that will hold the blob.
     * This is usually represented by the attribute 'name' of file input in
     * the front-end.
     * 
     * @param string $filePath The path of the file within testing environment.
     * 
     * @param bool $reset If set to true, the array $_FILES will be re-initialized.
     */
    public function addFile(string $fileIdx, string $filePath, bool $reset = false) {
        if ($reset) {
            $_FILES = [];
        }

        if (!isset($_FILES[$fileIdx])) {
            $_FILES[$fileIdx] = [];
            $_FILES[$fileIdx]['name'] = [];
            $_FILES[$fileIdx]['type'] = [];
            $_FILES[$fileIdx]['size'] = [];
            $_FILES[$fileIdx]['tmp_name'] = [];
            $_FILES[$fileIdx]['error'] = [];
        }
        $info = $this->extractPathAndName($filePath);
        $path = $info['path'].DS.$info['name'];

        $_FILES[$fileIdx]['name'][] = $info['name'];
        $_FILES[$fileIdx]['type'][] = mime_content_type($path);
        $_FILES[$fileIdx]['size'][] = filesize($path);
        $_FILES[$fileIdx]['tmp_name'][] = $path;
        $_FILES[$fileIdx]['error'][] = 0;
    }
    /**
     * Performs a call to an endpoint.
     * 
     * @param WebServicesManager $manager The services manager instance that is used to
     * manage the service.
     * 
     * @param string $requestMethod A string that represents the name of request
     * method such as 'get' or 'post'.
     * 
     * @param string $apiEndpointName The name of the endpoint that will be called such as 'add-user'.
     * This also can be the name of the class that implement the service such
     * as 'AddUserService::class'.
     * 
     * @param array $parameters A dictionary thar represents the parameters that
     * will be sent to the endpoint. The name is parameter name as it appears in
     * service implementation and its value is the value of the parameter.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     *  
     * @return string The method will return the output of the endpoint.
     */
    public function callEndpoint(WebServicesManager $manager, string $requestMethod, string $apiEndpointName, array $parameters = [], array $httpHeaders = []) : string {
        $method = strtoupper($requestMethod);
        $serviceName = $this->resolveServiceName($apiEndpointName);
        
        $this->setupRequest($method, $serviceName, $parameters, $httpHeaders);
        
        $manager->setOutputStream(fopen($this->getOutputFile(), 'w'));
        $manager->setRequest(Request::createFromGlobals());
        $manager->process();
        
        $result = $manager->readOutputStream();
        
        if (file_exists($this->getOutputFile())) {
            unlink($this->getOutputFile());
        }
        
        return $this->formatOutput($result);
    }
    
    /**
     * Resolves service name from class name or returns the name as-is.
     * 
     * @param string $nameOrClass Service name or class name
     * 
     * @return string The resolved service name
     */
    private function resolveServiceName(string $nameOrClass): string {
        if (class_exists($nameOrClass)) {
            $reflection = new \ReflectionClass($nameOrClass);
            
            if ($reflection->isSubclassOf(WebService::class)) {
                $constructor = $reflection->getConstructor();
                
                if ($constructor && $constructor->getNumberOfRequiredParameters() === 0) {
                    $service = $reflection->newInstance();
                    return $service->getName();
                }
            }
        }
        
        return $nameOrClass;
    }
    
    /**
     * Sets up the request environment.
     * 
     * @param string $method HTTP method
     * @param string $serviceName Service name
     * @param array $parameters Request parameters
     * @param array $httpHeaders HTTP headers
     */
    private function setupRequest(string $method, string $serviceName, array $parameters, array $httpHeaders) {
        putenv('REQUEST_METHOD=' . $method);
        
        // Normalize header names to lowercase for case-insensitive comparison
        $normalizedHeaders = [];
        foreach ($httpHeaders as $name => $value) {
            $normalizedHeaders[strtolower($name)] = $value;
        }
        
        if (in_array($method, [RequestMethod::POST, RequestMethod::PUT, RequestMethod::PATCH])) {
            $_POST = $parameters;
            $_POST['service'] = $serviceName;
            $_SERVER['CONTENT_TYPE'] = $normalizedHeaders['content-type'] ?? 'application/x-www-form-urlencoded';
        } else {
            $_GET = $parameters;
            $_GET['service'] = $serviceName;
        }
        
        foreach ($normalizedHeaders as $name => $value) {
            if ($name !== 'content-type') {
                $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $name))] = $value;
            }
        }
    }
    
    /**
     * Formats the output, attempting to pretty-print JSON if possible.
     * 
     * @param string $output Raw output
     * 
     * @return string Formatted output
     */
    private function formatOutput(string $output): string {
        try {
            $json = Json::decode($output);
            $json->setIsFormatted(true);
            return $json . '';
        } catch (JsonException $ex) {
            return $output;
        }
    }
    /**
     * Creates a formatted string from calling an API.
     * 
     * This helper method can be used to format JSON output of calling an API
     * and use it in assertions. The goal of the method is to initially format
     * the out put, display it as string and the developer copies the output
     * and modify it as needed.
     * 
     * @param string $output
     */
    public function format(string $output) {
        $expl = explode(self::NL, $output);
        $nl = '.self::NL\n';
        $count = count($expl);
        
        for ($x = 0 ; $x < count($expl) ; $x++) {
            if ($x + 1 == $count) {
                $nl = '';
            }
            echo ". '$expl[$x]]'".$nl;
        }
    }
    /**
     * Sends a DELETE request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function deleteRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::DELETE, $endpoint, $parameters, $httpHeaders);
    }
    /**
     * Sends a GET request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function getRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::GET, $endpoint, $parameters, $httpHeaders);
    }
    /**
     * Sends a POST request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function postRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::POST, $endpoint, $parameters, $httpHeaders);
    }
    /**
     * Sends a PUT request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function putRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::PUT, $endpoint, $parameters, $httpHeaders);
    }
    /**
     * Sends a PATCH request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function patchRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::PATCH, $endpoint, $parameters, $httpHeaders);
    }
    /**
     * Sends an OPTIONS request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function optionsRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::OPTIONS, $endpoint, $parameters, $httpHeaders);
    }
    /**
     * Sends a HEAD request to specific endpoint.
     * 
     * @param WebServicesManager $manager The manager which is used to manage the endpoint.
     * 
     * @param string $endpoint The name of the endpoint.
     * 
     * @param array $parameters An optional array of request parameters that can be
     * passed to the endpoint.
     * 
     * @param array $httpHeaders An optional associative array that can be used
     * to mimic HTTP request headers. The keys of the array are names of headers
     * and the value of each key represents the value of the header.
     * 
     * @return string The method will return the output that was produced by
     * the endpoint as string.
     */
    public function headRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
        return $this->callEndpoint($manager, RequestMethod::HEAD, $endpoint, $parameters, $httpHeaders);
    }
    private function extractPathAndName($absPath): array {
        $DS = DIRECTORY_SEPARATOR;
        $cleanPath = str_replace('\\', $DS, str_replace('/', $DS, trim($absPath)));
        $pathArr = explode($DS, $cleanPath);

        if (count($pathArr) != 0) {
            $fPath = '';
            $name = $pathArr[count($pathArr) - 1];

            for ($x = 0 ; $x < count($pathArr) - 1 ; $x++) {
                $fPath .= $pathArr[$x].$DS;
            }

            return [
                'path' => $fPath,
                'name' => $name
            ];
        }

        return [
            'name' => $cleanPath,
            'path' => ''
        ];
    }
}
