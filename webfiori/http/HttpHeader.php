<?php
namespace webfiori\http;

/**
 * A class that represents HTTP request or response header.
 *
 * For more information on HTTP headers, read 
 * https://datatracker.ietf.org/doc/html/rfc2616#section-4.2
 * 
 * @author Ibrahim
 */
class HttpHeader {
    private $headerName;
    private $headerValue;
    /**
     * Creates new instance of the class.
     * 
     * @param string $name The name of the header such as 'User-Agent'
     * 
     * @param string $value
     */
    public function __construct(string $name = '', string $value = '') {
        $this->headerName = 'http-header';
        $this->headerValue = 'http-val';
        $this->setName($name);
        $this->setValue($value);
    }
    /**
     * Returns string representation of the header.
     * 
     * @return string The returned string will consists of a name followed
     * by a colon (":") and the header value.
     */
    public function __toString() {
        return $this->getName().': '.$this->getValue();
    }
    /**
     * Returns a string that represents the name of the header.
     * 
     * @return string A string that represents the name of the header. Default
     * return value is 'http-header'
     */
    public function getName() : string {
        return $this->headerName;
    }
    /**
     * Returns the value of the header.
     * 
     * @return string A string that represents the value of the header. Default
     * return value is 'http-val'
     */
    public function getValue() : string {
        return $this->headerValue;
    }
    /**
     * Sets the name of the header.
     * 
     * @param string $name The name of the header. A valid header name must
     * follow following rules: only consist of the letters A-Z, a-z, underscore
     * and hyphen.
     * 
     * @return bool If the name is successfully set, true is returned. If
     * not set, the method will return false.
     */
    public function setName(string $name) : bool {
        $trimmed = strtolower(trim($name));

        if ($this->_validateheaderName($trimmed)) {
            $this->headerName = $trimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets the value of the header.
     * 
     * @param string $val A string that represents the value of the header.
     */
    public function setValue(string $val) {
        $this->headerValue = $val;
    }
    private static function _validateheaderName($name) {
        $len = strlen($name);

        if ($len == 0) {
            return false;
        }

        for ($x = 0 ; $x < $len ; $x++) {
            $char = $name[$x];

            if (!(($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || $char == '_' || $char == '-')) {
                return false;
            }
        }

        return true;
    }
}
