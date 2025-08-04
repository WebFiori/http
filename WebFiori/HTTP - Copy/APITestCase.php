<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2024 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace webfiori\http;

use PHPUnit\Framework\TestCase;
use webfiori\json\Json;
use webfiori\json\JsonException;
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
    const DEFAULT_OUTPUT_STREAM = __DIR__.DIRECTORY_SEPARATOR.'outputStream.txt';
    private $outputStreamPath;
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
        $manager->setOutputStream(fopen($this->getOutputFile(),'w'));
        $method = strtoupper($requestMethod);
        putenv('REQUEST_METHOD='.$method);
        
        if (class_exists($apiEndpointName)) {
            $service = new $apiEndpointName();
            
            if ($service instanceof AbstractWebService) {
                $apiEndpointName = $service->getName();
            }
        }
        if ($method == RequestMethod::POST || $method == RequestMethod::PUT || $method == RequestMethod::PATCH) {
            foreach ($parameters as $key => $val) {
                $_POST[$key] = $this->parseVal($val);
            }
            $_POST['service'] = $apiEndpointName;
            $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
            $this->unset($_POST, $parameters, $manager, $httpHeaders);
        } else {
            foreach ($parameters as $key => $val) {
                $_GET[$key] = $this->parseVal($val);
            }
            $_GET['service'] = $apiEndpointName;
            $this->unset($_GET, $parameters, $manager, $httpHeaders);
        }

        $retVal = $manager->readOutputStream();
        unlink($this->getOutputFile());
        
        try {
            $json = Json::decode($retVal);
            $json->setIsFormatted(true);
            return $json.'';
        } catch (JsonException $ex) {
            return $retVal;
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
    private function parseVal($val) {
        $type = gettype($val);
        
        if ($type == 'array') {
            $array = [];
            
            foreach ($val as $arrVal) {
                if (gettype($val) == 'string') {
                    $array[] = "'".$arrVal."'";
                } else {
                    $array[] = $arrVal;
                }
            }
            
            return implode(',', $array);
        } else if ($type == 'boolean') {
            return $type === true ? 'y' : 'n';
        }
        return $val;
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
    public function deletRequest(WebServicesManager $manager, string $endpoint, array $parameters = [], array $httpHeaders = []) : string {
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
    private function unset(array &$arr, array $params, WebServicesManager $m, array $httpHeaders) {
        foreach ($httpHeaders as $header => $value) {
            $trHeader = trim($header.'');
            $trVal = trim($value.'');
            if (strlen($trHeader) != 0) {
                $_SERVER['HTTP_'.strtoupper($trHeader)] = $trVal;
            }
        }
        $m->process();

        foreach ($params as $key => $val) {
            unset($arr[$key]);
        }
        
        foreach ($httpHeaders as $header => $value) {
            $trHeader = trim($header.'');
            unset($_SERVER['HTTP_'.strtoupper($trHeader)]);
        }
    }
}
