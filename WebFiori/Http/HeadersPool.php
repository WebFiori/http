<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2022 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * This class is used to manage HTTP message headers.
 *
 * @author Ibrahim
 */
class HeadersPool {
    /**
     * An array that contains HTTP headers.
     * 
     * @var array
     */
    private $headersArr;
    /**
     * Creates new instance of the class.
     */
    public function __construct() {
        $this->clear();
    }
    /**
     * Adds new HTTP header to the pool.
     * 
     * @param string $headerName The name of the header.
     * 
     * @param string $headerVal The value of the header.
     * 
     * @param string $replaceValue If the header is already exist and this parameter 
     * is specified, the method will override existing header with the specified
     * value with the given new value. Note that if no header was found which
     * has the given value, the header will be added as new one.
     * 
     * @return boolean If the header is added, the method will return true. If 
     * not added, the method will return false.
     * 
     */
    public function addHeader(string $headerName, string $headerVal, ?string $replaceValue = '') : bool {
        $trimmedHeader = strtolower(trim($headerName));
        $retVal = false;
        $header = new HttpHeader();

        if ($header->setName($headerName)) {
            $header->setValue($headerVal);

            if ($replaceValue !== null && strlen($replaceValue) != 0) {
                $hasHeader = $this->hasHeader($trimmedHeader, $replaceValue);
            } else {
                $hasHeader = false;
            }

            if ($hasHeader) {
                $this->removeHeader($trimmedHeader, $replaceValue);
            }
            $this->headersArr[] = $header;
            $retVal = true;
        }

        return $retVal;
    }
    /**
     * Removes all added headers from the pool.
     */
    public function clear() {
        $this->headersArr = [];
    }
    /**
     * Returns the value(s) of specific HTTP header.
     * 
     * @param string $name The name of the header.
     * 
     * @return array If such header exist, the method will return an array 
     * that contains the values of the header. If the header does not exist, the 
     * method will return an empty array.
     * 
     */
    public function getHeader(string $name) : array {
        return array_map(function ($headerObj)
        {
            return $headerObj->getValue();
        }, $this->getHeaderAsObj($name));
    }

    /**
     * Returns the value(s) of specific HTTP header as an array of objects.
     *
     * @param string $name The name of the header. Note that if name contains
     * underscore (_), it will be replaced by a dash (-).
     *
     * @return array If such header exist, the method will return an array
     * that contains the values of the header stored as objects of type
     * HttpHeader. If the header does not exist, the array will be empty.
     */
    public function getHeaderAsObj(string $name) : array {
        $retVal = [];
        $trimmed = str_replace('_', '-', strtolower(trim($name)));
        
        foreach ($this->getHeaders() as $headerObj) {
            if ($headerObj->getName() == $trimmed) {
                $retVal[] = $headerObj;
            }
        }

        return $retVal;
    }
    /**
     * Returns an array that contains all added headers.
     * 
     * @return array An array that contains objects of type HttpHeader.
     * 
     */
    public function getHeaders() : array {
        return $this->headersArr;
    }
    /**
     * Checks if the response will have specific header or not.
     * 
     * This method will only check for headers which are added using the method 
     * Response::addHeader().
     * 
     * @param string $name The name of the header (such as 'content-type'). 
     * 
     * @param string $val An optional value to check for. Default is null 
     * which means only check for the name.
     * 
     * @return boolean If a header which has the given name exist, the method 
     * will return true. If a value is specified and a match is fond, the 
     * method will return true. Other than that, the method will return true.
     * 
     */
    public function hasHeader(string $name, ?string $val) : bool {
        $headers = $this->getHeaderAsObj($name);
        $trimmedVal = trim($val.'');
        if ($val === null || strlen($trimmedVal) == 0) {
            return count($headers) !== 0;
        }

        foreach ($headers as $obj) {
            if ($obj->getValue() == $trimmedVal) {
                return true;
            }
        }

        return false;
    }
    /**
     * Removes specific header from the pool.
     * 
     * @param string $name The name of the header that will be removed.
     * 
     * @param string $val If the header is added with multiple values, this
     * can be used to remove specific one with specific value. If not provided,
     * all headers will be removed.
     *
     * @return bool If removed, true is returned. False otherwise.
     */
    public function removeHeader(string $name, ?string $val = '') : bool {
        $tempArr = [];
        $trimmed = strtolower(trim($name));
        $removed = false;

        foreach ($this->getHeaders() as $headerObj) {
            if ($headerObj->getName() == $trimmed) {
                if ($val !== null && strlen($val) != 0 && $headerObj->getValue() != $val) {
                    $tempArr[] = $headerObj;
                } else {
                    $removed = true;
                }
            } else {
                $tempArr[] = $headerObj;
            }
        }
        $this->headersArr = $tempArr;

        return $removed;
    }
}
