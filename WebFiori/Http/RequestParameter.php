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

use WebFiori\Http\OpenAPI\Schema;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;
/**
 * A class that represents request parameter.
 * 
 * Request parameter can be part of query string in case of 
 * GET and DELETE calls and in request body in case of 
 * PUT or POST requests
 * 
 * @author Ibrahim
 * 
 */
class RequestParameter implements JsonI {
    /**
     * A boolean value that is set to true in case the 
     * basic filter will be applied before custom one.
     * 
     * @var boolean
     * 
     */
    private $applyBasicFilter;
    /**
     * A callback that is used to make a custom filtered value.
     * 
     * @var callable
     * 
     */
    private $customFilterFunc;
    /**
     * The default value that will be used in case of parameter filter 
     * failure.
     * 
     * @var mixed 
     * 
     */
    private $default;
    /**
     * The description of the parameter.
     * 
     * @var string
     * 
     */
    private $desc;
    /**
     * A boolean value that can be set to true to allow empty strings.
     * 
     * @var boolean 
     * 
     */
    private $isEmptyStrAllowed;
    /**
     * Indicates either the attribute is optional or not.
     * 
     * @var bool true if the parameter is optional.
     * 
     */
    private $isOptional;
    /**
     * The minimum length. Used if the parameter type is string.
     * 
     * @var double|null
     */
    private $maxLength;
    /**
     * The maximum value. Used if the parameter type is numeric.
     * 
     * @var double|null
     * 
     */
    private $maxVal;
    /**
     * The minimum length. Used if the parameter type is string.
     * 
     * @var double|null
     * 
     */
    private $minLength;
    /**
     * The minimum value. Used if the parameter type is numeric.
     * 
     * @var double|null
     * 
     */
    private $minVal;
    /**
     * The name of the parameter.
     * 
     * @var string
     * 
     */
    private $name;
    /**
     * The type of the data the parameter will represent.
     * 
     * @var string
     * 
     */
    private $type;
    /**
     * An array of request methods at which the parameter must exist.
     * 
     * @var array
     * 
     */
    private $methods;
    /**
     * Creates new instance of the class.
     * 
     * @param string $name The name of the parameter as it appears in the request body. 
     * It must be a valid name. If the given name is invalid, the parameter 
     * name will be set to 'a-parameter'. Valid name must comply with the following 
     * rules:
     * <ul>
     * <li>It can contain the letters [A-Z] and [a-z].</li>
     * <li>It can contain the numbers [0-9].</li>
     * <li>It can have the character '-' and the character '_'.</li>
     * </ul>
     * 
     * @param string $type The type of the data that will be in the parameter stored 
     * by the parameter. Supported types are:
     * <ul>
     * <li>string</li>
     * <li>integer</li>
     * <li>email</li>
     * <li>float</li>
     * <li>url</li>
     * <li>boolean</li>
     * <li>array</li>
     * <li>json-obj</li>
     * </ul> 
     * If invalid type is given or no type is provided, 'string' will be used by 
     * default.
     * 
     * @param bool $isOptional Set to true if the parameter is optional. Default 
     * is false.
     */
    public function __construct(string $name, string $type = 'string', bool $isOptional = false) {
        if (!$this->setName($name)) {
            $this->setName('a-parameter');
        }
        $this->setIsOptional($isOptional);

        if (!$this->setType($type)) {
            $this->type = 'string';
        }
        $this->applyBasicFilter = true;
        $this->isEmptyStrAllowed = false;
        $this->methods = [];
    }
    /**
     * Returns a string that represents the object.
     * 
     * @return string A string in the following format:
     * <p>
     * RequestParameter[<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Name => 'a_name'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Type => 'a_type'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Description => 'a_desc'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Is Optional => 'true'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Default => 'a_default'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Minimum Value => 'a_number'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Maximum Value => 'a_number'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Minimum Length => 'a_number'<br/>
     * &nbsp;&nbsp;&nbsp;&nbsp;Maximum Length => 'a_number'
     * <br/>]
     * </p>
     * If any of the values is null, the value will be shown as 'null'.
     * 
     */
    public function __toString() {
        $retVal = "RequestParameter[\n";
        $retVal .= "    Name => '".$this->getName()."',\n";
        $retVal .= "    Type => '".$this->getType()."',\n";
        $descStr = $this->getDescription() === null ? 'null' : $this->getDescription();
        $retVal .= "    Description => '$descStr',\n";
        $isOptionalStr = $this->isOptional() ? 'true' : 'false';
        $retVal .= "    Is Optional => '$isOptionalStr',\n";
        $defaultStr = $this->getDefault() === null ? 'null' : $this->getDefault();
        $retVal .= "    Default => '$defaultStr',\n";
        $min = $this->getMinValue() === null ? 'null' : $this->getMinValue();
        $retVal .= "    Minimum Value => '$min',\n";
        $max = $this->getMaxValue() === null ? 'null' : $this->getMaxValue();
        $retVal .= "    Maximum Value => '$max',\n";
        $minLength = $this->getMinLength() === null ? 'null' : $this->getMinLength();
        $retVal .= "    Minimum Length => '$minLength',\n";
        $maxLength = $this->getMaxLength() === null ? 'null' : $this->getMaxLength();

        return $retVal."    Maximum Length => '$maxLength'\n]\n";
    }
    /**
     * Creates an object of the class given an associative array of options.
     * 
     * @param array $options An associative array of 
     * options. The array can have the following indices:
     * <ul>
     * <li><b>name</b>: The name of the parameter. If invalid name is provided, 
     * the value 'a-parameter' is used. If it is not provided, no 
     * parameter will be created.</li>
     * <li><b>type</b>: The datatype of the parameter. If not provided, 'string' is used.</li>
     * <li><b>optional</b>: A boolean. If set to true, it means the parameter is 
     * optional. If not provided, 'false' is used.</li>
     * <li><b>min</b>: Minimum value of the parameter. Applicable only for 
     * numeric types.</li>
     * <li><b>max</b>: Maximum value of the parameter. Applicable only for 
     * numeric types.</li>
     * <li><b>min-length</b>: Minimum length of the parameter. Applicable only for 
     * string types.</li>
     * <li><b>max-length</b>: Maximum length of the parameter. Applicable only for 
     * string types.</li>
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
     * @return null|RequestParameter If the given request parameter is created,
     *  the method will return an object of type 'RequestParameter'. 
     * If it was not created for any reason, the method will return null.
     * 
     */
    public static function create(array $options) : ?RequestParameter {
        if (isset($options[ParamOption::NAME])) {
            $paramType = $options[ParamOption::TYPE] ?? 'string';
            $param = new RequestParameter($options[ParamOption::NAME], $paramType);
            self::checkParamAttrs($param, $options);

            return $param;
        }

        return null;
    }
    /**
     * Returns the function that is used as a custom filter 
     * for the parameter.
     * 
     * @return callback|null The function that is used as a custom filter 
     * for the parameter. If not set, the method will return null.
     * 
     */
    public function getCustomFilterFunction() {
        return $this->customFilterFunc;
    }
    /**
     * Returns the default value to use in case the parameter is 
     * not provided.
     * 
     * @return mixed|null The default value to use in case the parameter is 
     * not provided. If no default value is provided, the method will 
     * return null.
     * 
     */
    public function getDefault() {
        return $this->default;
    }
    /**
     * Returns the description of the parameter.
     * 
     * @return string|null The description of the parameter. If the description is 
     * not set, the method will return null.
     * 
     */
    public function getDescription() {
        return $this->desc;
    }
    /**
     * Returns the maximum length the parameter can accept.
     * 
     * This method apply if the type of the parameter is string.
     * 
     * @return double|null The maximum length the parameter can accept.
     * If the request parameter type is not string, the method will return 
     * null.
     * 
     */
    public function getMaxLength() {
        return $this->maxLength;
    }
    /**
     * Returns the maximum numeric value the parameter can accept.
     * 
     * This method apply only to integer type.
     * 
     * @return double|null The maximum numeric value the parameter can accept.
     * If the request parameter type is not numeric, the method will return 
     * null.
     * 
     */
    public function getMaxValue() {
        return $this->maxVal;
    }
    /**
     * Returns the minimum length the parameter can accept.
     * 
     * This method apply only to string type.
     * 
     * @return double|null The minimum length the parameter can accept.
     * If the request parameter type is not string, the method will return 
     * null.
     * 
     */
    public function getMinLength() {
        return $this->minLength;
    }
    /**
     * Returns the minimum numeric value the parameter can accept.
     * 
     * This method apply only to double and integer types.
     * 
     * @return double|null The minimum numeric value the parameter can accept.
     * If the request parameter type is not numeric, the method will return 
     * null.
     * 
     */
    public function getMinValue() {
        return $this->minVal;
    }
    /**
     * Returns the name of the parameter.
     * 
     * @return string The name of the parameter.
     * 
     */
    public function getName() : string {
        return $this->name;
    }
    /**
     * Returns the type of the parameter.
     * 
     * @return string The type of the parameter (Such as 'string', 'email', 'integer').
     * 
     */
    public function getType() : string {
        return $this->type;
    }
    /**
     * Checks if we need to apply basic filter or not 
     * before applying custom filter callback.
     * 
     * @return bool The method will return true 
     * if the basic filter will be applied before applying custom filter. If no custom 
     * filter is set, the method will return true by default.
     * 
     */
    public function isBasicFilter() : bool {
        return $this->applyBasicFilter;
    }
    /**
     * Checks if empty strings are allowed as values for the parameter.
     * 
     * If the property value is not updated using the method 
     * RequestParameter::setIsEmptyStringAllowed(), The method will return 
     * default value which is false.
     * 
     * @return bool true if empty strings are allowed as values for the parameter. 
     * false if not.
     * 
     */
    public function isEmptyStringAllowed() : bool {
        return $this->isEmptyStrAllowed;
    }
    /**
     * Returns a boolean value that can be used to tell if the parameter is 
     * optional or not.
     * 
     * @return bool true if the parameter is optional and false 
     * if not.
     * 
     */
    public function isOptional() : bool {
        return $this->isOptional;
    }
    /**
     * Sets a callback method to work as a filter for request parameter.
     * 
     * The callback method will have 3 parameters passed to it:
     * <ul>
     * <li>Original value without filtering.</li>
     * <li>The value with basic filtering rules applied to it.</li>
     * <li>An object of type RequestParameter.</li>
     * </ul> 
     * <p>If the parameter $applyBasicFilter is set to false, the second parameter 
     * will have the value 'NOT_APPLICABLE'.</p>
     * <p>The object of type <b>RequestParameter</b> 
     * will contain original information for the filter.</p> The method 
     * must be implemented in a way that makes it return false or null if the 
     * parameter has invalid value. If the parameter is filtered and 
     * was validated, the method must return the valid and filtered 
     * value.
     * 
     * @param callback $function A callback function. 
     * 
     * @param bool $applyBasicFilter If set to true, 
     * the basic filter will be applied to the parameter. Default 
     * is true.
     *
     * 
     */
    public function setCustomFilterFunction(callable $function, bool $applyBasicFilter = true) {
        $this->customFilterFunc = $function;
        $this->applyBasicFilter = $applyBasicFilter === true;
    }

    /**
     * Sets a default value for the parameter to use if the parameter is 
     * not provided Or when the filter fails.
     * 
     * This method can be used to include a default value for the parameter if 
     * it is optional or in case the filter was not able to filter given value.
     * 
     * @param mixed $val default value for the parameter to use.
     * 
     * @return bool If the default value is set, the method will return true. 
     * If it is not set, the method will return false.
     * 
     */
    public function setDefault($val) : bool {
        $valType = gettype($val);
        $RPType = $this->getType();

        if ($valType == $RPType || 
           ($valType == 'integer' && $RPType == ParamType::DOUBLE) ||
           ($valType == 'double' && $RPType == ParamType::DOUBLE) ||
           (($RPType == ParamType::EMAIL || $RPType == ParamType::URL) && $valType == ParamType::STRING) || 
           ($val instanceof Json && $RPType == 'json-obj')) {
            $this->default = $val;

            return true;
        }

        return false;
    }
    /**
     * Sets the description of the parameter.
     * 
     * This method is used to document the API. Used to help front-end developers.
     * 
     * @param string $desc Parameter description.
     * 
     */
    public function setDescription(string $desc) {
        $this->desc = trim($desc);
    }
    /**
     * Allow or disallow empty strings as values for the parameter.
     * 
     * The value of the attribute will be updated only if the type of the 
     * parameter is set to 'string'.
     * 
     * @param bool $bool true to allow empty strings and false to disallow 
     * empty strings.
     * 
     * @return bool The method will return true if the property is updated. 
     * If datatype of the request parameter is not string, The method will 
     * not update the property value and will return false.
     * 
     */
    public function setIsEmptyStringAllowed(bool $bool) : bool {
        if ($this->getType() == ParamType::STRING) {
            //in php 5.6, primitive type hinting is not allowed.
            //this will resolve the issue.
            $this->isEmptyStrAllowed = $bool;

            return true;
        }

        return false;
    }
    /**
     * Sets the value of the property 'isOptional'.
     * 
     * @param bool $bool True to make the parameter optional. False to make it mandatory.
     * 
     */
    public function setIsOptional(bool $bool) {
        $this->isOptional = $bool === true;
    }
    /**
     * Sets the maximum length that the parameter can accept.
     * 
     * The value will be updated 
     * only if:
     * <ul>
     * <li>Provided value must be greater than 0.</li>
     * <li>The request parameter type is string ('url' or 'string' or 'email').</li>
     * <li>The given value is greater than RequestParameter::getMinLength()</li>
     * </ul>
     * 
     * @param int $val The maximum length of the parameter.
     * 
     * @return bool The method will return true once the maximum length 
     * is updated. false if not.
     */
    public function setMaxLength(int $val) : bool {
        $type = $this->getType();

        if (in_array($type, ParamType::getStringTypes())) {
            $min = $this->getMinLength() === null ? 0 : $this->getMinLength();

            if ($val >= $min && $val > 0) {
                $this->maxLength = $val;

                return true;
            }
        }

        return false;
    }

    /**
     * Sets the maximum value.
     *
     * The value will be updated
     * only if:
     * <ul>
     * <li>The request parameter type is numeric ('integer' or 'float').</li>
     * <li>The given value is greater than RequestParameter::getMinValue()</li>
     * </ul>
     *
     * @param float $val The maximum value to set.
     *
     * @return bool The method will return true once the maximum value
     * is updated. false if not.
     *
     */
    public function setMaxValue(float $val) : bool {
        $type = $this->getType();

        if (in_array($type, ParamType::getNumericTypes())) {
            $min = $this->getMinValue();

            if ($min !== null && $val > $min) {
                $this->maxVal = $type === ParamType::INT ? intval($val) : $val;

                return true;
            }
        }

        return false;
    }
    /**
     * Sets the minimum length that the parameter can accept.
     * 
     * The value will be updated 
     * only if:
     * <ul>
     * <li>Provided value must be greater than 0.</li>
     * <li>The request parameter type is string ('url' or 'string' or 'email').</li>
     * <li>The given value is less than RequestParameter::getMaxLength()</li>
     * </ul>
     * 
     * @param int $val The minimum length of the parameter.
     * 
     * @return bool The method will return true once the minimum length 
     * is updated. false if not.
     */
    public function setMinLength(int $val) : bool {
        $type = $this->getType();

        if (in_array($type, ParamType::getStringTypes())) {
            $max = $this->getMaxLength() === null ? PHP_INT_MAX : $this->getMaxLength();

            if ($val <= $max && $val > 0) {
                $this->minLength = $val;

                return true;
            }
        }

        return false;
    }
    /**
     * Sets the minimum value that the parameter can accept.
     * 
     * The value will be updated 
     * only if:
     * <ul>
     * <li>The request parameter type is numeric ('integer' or 'float').</li>
     * <li>The given value is less than RequestParameter::getMaxValue()</li>
     * </ul>
     * 
     * @param float $val The minimum value to set.
     * 
     * @return bool The method will return true once the minimum value 
     * is updated. false if not.
     * 
     */
    public function setMinValue(float $val) : bool {
        $type = $this->getType();

        if (in_array($type, ParamType::getNumericTypes())) {
            $max = $this->getMaxValue();

            if ($max !== null && $val < $max) {
                $this->minVal = $type == ParamType::INT ? intval($val) : $val;

                return true;
            }
        }

        return false;
    }
    /**
     * Sets the name of the parameter.
     * 
     * A valid parameter name must 
     * follow the following rules:
     * <ul>
     * <li>It can contain the letters [A-Z] and [a-z].</li>
     * <li>It can contain the numbers [0-9].</li>
     * <li>It can have the character '-' and the character '_'.</li>
     * </ul>
     * 
     * @param string $name The name of the parameter. 
     * 
     * @return bool If the given name is valid, the method will return 
     * true once the name is set. false is returned if the given 
     * name is invalid.
     * 
     */
    public function setName(string $name) : bool {
        $nameTrimmed = trim($name);

        if (WebService::isValidName($nameTrimmed)) {
            $this->name = $nameTrimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets the type of the parameter.
     * 
     * @param string $type The type of the parameter. It must be a value 
     * form the constants which exist in the class 'ParamTypes'.
     * 
     * @return bool true is returned if the type is updated. false 
     * if not.
     * 
     */
    public function setType(string $type) : bool {
        $sType = strtolower(trim($type));

        if ($sType == 'float') {
            $sType = 'double';
        } else if ($sType == 'int') {
            $sType = 'integer';
        }

        if (in_array($sType, ParamType::getTypes())) {
            $this->type = $sType;

            if ($sType == ParamType::DOUBLE) {
                $this->maxVal = defined('PHP_FLOAT_MAX') ? PHP_FLOAT_MAX : 1.7976931348623E+308;
                $this->minVal = defined('PHP_FLOAT_MIN') ? PHP_FLOAT_MIN : 2.2250738585072E-308;
            } else if ($sType == ParamType::INT) {
                $this->minVal = defined('PHP_INT_MIN') ? PHP_INT_MIN : ~PHP_INT_MAX;
                $this->maxVal = PHP_INT_MAX;
            } else {
                $this->maxVal = null;
                $this->minVal = null;
            }

            return true;
        }

        return false;
    }
    /**
     * Returns a Json object that represents the request parameter.
     * 
     * 
     * @return Json An object of type Json. 
     * 
     */
    public function toJSON() : Json {
        $json = new Json();
        $json->add('name', $this->getName());
        
        $methods = $this->getMethods();
        // Default to 'query' for GET/DELETE, 'body' for others
        if (count($methods) === 0 || in_array(RequestMethod::GET, $methods) || in_array(RequestMethod::DELETE, $methods)) {
            $json->add('in', 'query');
        } else {
            $json->add('in', 'body');
        }
        
        $json->add('required', !$this->isOptional());
        
        if ($this->getDescription() !== null) {
            $json->add('description', $this->getDescription());
        }
        
        $json->add('schema', $this->getSchema());
        
        return $json;
    }
    private function getSchema() : Json {
        return Schema::fromRequestParameter($this)->toJson();
    }
    
    /**
     * 
     * @param RequestParameter $param
     * @param array $options
     */
    private static function checkParamAttrs(RequestParameter $param, array $options) {
        $isOptional = $options[ParamOption::OPTIONAL] ?? false;
        $param->setIsOptional($isOptional);

        if (isset($options[ParamOption::FILTER])) {
            $param->setCustomFilterFunction($options[ParamOption::FILTER]);
        }

        if (isset($options[ParamOption::MIN])) {
            $param->setMinValue($options[ParamOption::MIN]);
        }

        if (isset($options[ParamOption::MAX])) {
            $param->setMaxValue($options[ParamOption::MAX]);
        }

        if (isset($options[ParamOption::MIN_LENGTH])) {
            $param->setMinLength($options[ParamOption::MIN_LENGTH]);
        }

        if (isset($options[ParamOption::MAX_LENGTH])) {
            $param->setMaxLength($options[ParamOption::MAX_LENGTH]);
        }

        if (isset($options[ParamOption::EMPTY])) {
            $param->setIsEmptyStringAllowed($options[ParamOption::EMPTY]);
        }

        if (isset($options[ParamOption::METHODS])) {
            $type = gettype($options[ParamOption::METHODS]);
            if ($type == 'string') {
                $param->addMethod($options[ParamOption::METHODS]);
            } else if ($type == 'array') {
                $param->addMethods($options[ParamOption::METHODS]);
            }
        }

        if (isset($options[ParamOption::DEFAULT])) {
            $param->setDefault($options[ParamOption::DEFAULT]);
        }

        if (isset($options[ParamOption::DESCRIPTION])) {
            $param->setDescription($options[ParamOption::DESCRIPTION]);
        }
    }
    /**
     * Returns an array of request methods at which the parameter must exist.
     * 
     * @return array An array of request method names (e.g., ['GET', 'POST']).
     */
    public function getMethods(): array {
        return $this->methods;
    }
    
    /**
     * Adds a request method to the parameter.
     * 
     * @param string $requestMethod The request method name (e.g., 'GET', 'POST').
     * 
     * @return RequestParameter Returns self for method chaining.
     */
    public function addMethod(string $requestMethod): RequestParameter {
        $method = strtoupper(trim($requestMethod));
        if (!in_array($method, $this->methods) && in_array($method, RequestMethod::getAll())) {
            $this->methods[] = $method;
        }
        return $this;
    }
    
    /**
     * Adds multiple request methods to the parameter.
     * 
     * @param array $arr An array of request method names.
     * 
     * @return RequestParameter Returns self for method chaining.
     */
    public function addMethods(array $arr): RequestParameter {
        foreach ($arr as $method) {
            $this->addMethod($method);
        }
        return $this;
    }
}
