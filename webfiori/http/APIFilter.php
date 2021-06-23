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

use Exception;
use webfiori\json\Json;
/**
 * A class used to validate and sanitize request parameters.
 * 
 * This class is the core class which is used to manage and set request 
 * parameters.
 * 
 * @author Ibrahim
 * 
 * @version 1.2.3
 */
class APIFilter {
    /**
     * A constant that indicates a given value is invalid.
     * 
     * @var string A string that indicates a given value is invalid.
     * 
     * @since 1.2.2
     */
    const INVALID = 'INV';

    /**
     * An array that will contains filtered data.
     * 
     * @var array
     * 
     * @since 1.0 
     */
    private $inputs = [];
    private $inputStreamPath;
    /**
     * An array that contains non-filtered data (original).
     * 
     * @var array
     * 
     * @since 1.2 
     */
    private $nonFilteredInputs = [];
    /**
     * Array that contains filter definitions.
     * 
     * @var array
     * 
     * @since 1.0 
     */
    private $paramDefs = [];
    /**
     * Adds a new request parameter to the filter.
     * 
     * @param RequestParameter $reqParam The request parameter that will be added.
     * 
     * @since 1.1
     */
    public function addRequestParameter(RequestParameter $reqParam) {
        if ($reqParam instanceof RequestParameter) {
            $paramIdx = 'parameter';
            $filterIdx = 'filters';
            $optIdx = 'options';
            $attribute = [
                $paramIdx => $reqParam,
                $filterIdx => [],
                $optIdx => [$optIdx => []]
            ];

            if ($reqParam->getDefault() !== null) {
                $attribute[$optIdx][$optIdx]['default'] = $reqParam->getDefault();
            }

            if ($reqParam->getCustomFilterFunction() != null) {
                $attribute[$optIdx]['filter-func'] = $reqParam->getCustomFilterFunction();
            }
            $paramType = $reqParam->getType();

            if ($paramType == ParamTypes::INT) {
                if ($reqParam->getMaxVal() !== null) {
                    $attribute[$optIdx][$optIdx]['max_range'] = $reqParam->getMaxVal();
                }

                if ($reqParam->getMinVal() !== null) {
                    $attribute[$optIdx][$optIdx]['min_range'] = $reqParam->getMinVal();
                }
                array_push($attribute[$filterIdx], FILTER_SANITIZE_NUMBER_INT);
                array_push($attribute[$filterIdx], FILTER_VALIDATE_INT);
            } else if ($paramType == ParamTypes::STRING) {
                $attribute[$optIdx][$optIdx]['allow-empty'] = $reqParam->isEmptyStringAllowed();
                array_push($attribute[$filterIdx], FILTER_DEFAULT);
            } else if ($paramType == ParamTypes::DOUBLE) {
                array_push($attribute[$filterIdx], FILTER_SANITIZE_NUMBER_FLOAT);
            } else if ($paramType == ParamTypes::EMAIL) {
                array_push($attribute[$filterIdx], FILTER_SANITIZE_EMAIL);
                array_push($attribute[$filterIdx], FILTER_VALIDATE_EMAIL);
            } else if ($paramType == ParamTypes::URL) {
                array_push($attribute[$filterIdx], FILTER_SANITIZE_URL);
                array_push($attribute[$filterIdx], FILTER_VALIDATE_URL);
            } else {
                array_push($attribute[$filterIdx], FILTER_DEFAULT);
            }
            array_push($this->paramDefs, $attribute);
        }
    }
    /**
     * Clears the arrays that are used to store filtered and not-filtered variables.
     * 
     * @since 1.2.2
     */
    public function clearInputs() {
        $this->inputs = [];
        $this->nonFilteredInputs = [];
    }
    /**
     * Clears filter parameters. 
     * 
     * @since 1.1
     */
    public function clearParametersDef() {
        $this->paramDefs = [];
    }
    /**
     * Filter the values of an associative array.
     * 
     * The filtering algorithm will work as follows:
     * <ul>
     * <li>First, check if $arr['param-name'] is set.</li>
     * <li>If not set, check if its optional. If optional and default value is 
     * given, then use default value. Else, set the filtered value to null.</li>
     * <li>If arr['param-name'] is given, then do the following steps:
     * <ul>
     * <li>First, apply basic filtering (if applicable).</li>
     * <li>If custom filter is provided, then apply it.</li>
     * </ul>
     * </li>
     * </ul>
     * 
     * @param APIFilter $apiFilter An instance of the class 'APIFilter' that 
     * contains filter constrains.
     * 
     * @param array $arr An associative array of values which will be filtered.
     * 
     * @return array The method will return an associative array which has two 
     * indices. The index with key 'filtered' will contain an array which 
     * has all values filtered. The index which has the key 'non-filtered' 
     * will contain the original values.
     * 
     * @since 1.2.2
     */
    public static function filter(APIFilter $apiFilter, array $arr) {
        $filteredIdx = 'filtered';
        $noFIdx = 'non-filtered';
        $retVal = [
            $filteredIdx => [],
            $noFIdx => []
        ];
        $paramIdx = 'parameter';
        $optIdx = 'options';

        $filterDef = $apiFilter->getFilterDef();

        foreach ($filterDef as $def) {
            $name = $def[$paramIdx]->getName();
            $paramType = $def[$paramIdx]->getType();
            $defaultVal = $def[$paramIdx]->getDefault();

            if (isset($arr[$name])) {
                $toBeFiltered = urldecode($arr[$name]);
                $retVal[$noFIdx][$name] = $toBeFiltered;

                if (isset($def[$optIdx]['filter-func'])) {
                    $retVal[$filteredIdx][$name] = self::_applyCustomFilterFunc($def, $toBeFiltered);
                } else {
                    $retVal[$filteredIdx][$name] = self::_applyBasicFilterOnly($def, $toBeFiltered);
                }
                $booleanCheck = $paramType == 'boolean' && $retVal[$filteredIdx][$name] === true || $retVal[$filteredIdx][$name] === false;

                if (!$booleanCheck && $retVal[$filteredIdx][$name] == self::INVALID && $defaultVal !== null) {
                    $retVal[$filteredIdx][$name] = $defaultVal;
                }
            } else if ($def[$paramIdx]->isOptional()) {
                if ($defaultVal !== null) {
                    $retVal[$filteredIdx][$name] = $defaultVal;
                    $retVal[$noFIdx][$name] = $defaultVal;
                } else {
                    $retVal[$filteredIdx][$name] = null;
                    $retVal[$noFIdx][$name] = null;
                }
            }
        }

        return $retVal;
    }
    /**
     * Validate and sanitize GET parameters.
     * 
     * GET parameters are usually sent when request method is GET or DELETE.
     * The validation and sanitization algorithm will work as follows:
     * <ul>
     * <li>First, check if $_GET['param-name'] is set.</li>
     * <li>If not set, check if its optional. If optional and default value is 
     * given, then use default value. Else, set the filtered value to null.</li>
     * <li>If $_GET['param-name'] is given, then do the following steps:
     * <ul>
     * <li>First, apply basic validation and sanitization (if applicable).</li>
     * <li>If custom validation and sanitization function is provided, then apply it.</li>
     * </ul>
     * </li>
     * </ul>
     * 
     * @since 1.0
     */
    public final function filterGET() {
        $this->clearInputs();

        $filterResult = $this->filter($this, $_GET);
        $this->inputs = $filterResult['filtered'];
        $this->nonFilteredInputs = $filterResult['non-filtered'];
    }
    /**
     * Validate and sanitize POST parameters.
     * 
     * POST parameters are usually sent when request method is POST or PUT.
     * The validation and sanitization algorithm will work as follows:
     * <ul>
     * <li>First, check if $_POST['param-name'] is set.</li>
     * <li>If not set, check if its optional. If optional and default value is 
     * given, then use default value. Else, set the filtered value to null.</li>
     * <li>If $_POST['param-name'] is given, then do the following steps:
     * <ul>
     * <li>First, apply basic validation and sanitization (if applicable).</li>
     * <li>If custom validation and sanitization function is provided, then apply it.</li>
     * </ul>
     * </li>
     * </ul>
     * 
     * @since 1.0
     */
    public final function filterPOST() {
        $this->clearInputs();
        $contentTypeHeader = isset($_SERVER['CONTENT_TYPE']) ? filter_var($_SERVER['CONTENT_TYPE']) : null;

        if ($contentTypeHeader !== null && $contentTypeHeader !== false) {
            $contentType = trim(explode(';', $contentTypeHeader)[0]);
        } else {
            $contentType = null;
        }

        if ($contentType == 'application/json') {
            $this->_jsonBody();
        } else {
            $filterResult = $this->filter($this, $_POST);
            $this->inputs = $filterResult['filtered'];
            $this->nonFilteredInputs = $filterResult['non-filtered'];
        }
    }
    /**
     * Returns an array that contains filter constraints.
     * 
     * @return array An array that contains filter constraints.
     * 
     * @since 1.2.2
     */
    public function getFilterDef() {
        return $this->paramDefs;
    }
    /**
     * Returns an associative array or object of type 'Json' that contains 
     * request body inputs.
     * 
     * The data in the array or the object will have the filters applied to. 
     * Note that if parameter type is 'json-obj', no basic filtering will 
     * be applied. Only custom filter.
     * 
     * @return array|null|Json The array that contains request inputs. If no data was 
     * filtered, the method will return null. If the request content type was 
     * 'application/json', the method will return an instance of the class 
     * 'Json' that has all JSON information.
     * 
     * @since 1.0
     */
    public function getInputs() {
        return $this->inputs;
    }
    /**
     * Returns a string that represents input stream path.
     * 
     * @return string|null
     * 
     * @since 1.2.3
     */
    public function getInputStreamPath() {
        return $this->inputStreamPath;
    }
    /**
     * Returns an associative array or 'Json' object that contains request 
     * inputs without filters applied.
     * 
     * @return array|Json|null The array that contains request inputs. If request content 
     * type is 'application/json', the method will return an object of type 
     * 'Json'.
     * 
     * @since 1.2
     */
    public final function getNonFiltered() {
        return $this->nonFilteredInputs;
    }
    /**
     * Sets the stream at which the filter will use to read the inputs.
     * 
     * This can be used to test the filter if body content type is 
     * 'application/json'.
     * 
     * @param string|resource $pathOrResource A file that contains JSON or 
     * a stream which was opened using a function like 'fopen()'.
     * 
     * @return boolean If input stream is successfully set, the method will 
     * return true. False otherwise.
     * 
     * @since 1.2.3
     */
    public function setInputStream($pathOrResource) {
        if (is_resource($pathOrResource)) {
            $meat = stream_get_meta_data($pathOrResource);
            $this->inputStreamPath = $meat['uri'];

            return true;
        } 

        $trimmed = trim($pathOrResource);

        if (strlen($trimmed) > 0) {
            return $this->setInputStreamHelper($trimmed, 'r+');
        }

        return false;
    }
    private static function _applyBasicFilterOnly($def,$toBeFiltered) {
        $toBeFiltered = strip_tags($toBeFiltered);

        $paramObj = $def['parameter'];
        $paramType = $paramObj->getType();
        $optIdx = 'options'; 

        if ($paramType == ParamTypes::BOOL) {
            $returnVal = self::_filterBoolean($toBeFiltered);
        } else if ($paramType == ParamTypes::ARR) {
            $returnVal = self::_filterArray(filter_var($toBeFiltered));
        } else {
            $returnVal = filter_var($toBeFiltered);

            foreach ($def['filters'] as $val) {
                $returnVal = filter_var($returnVal, $val, $def[$optIdx]);
            }

            if ($returnVal === false || 
            (($paramType == ParamTypes::URL || $paramType == ParamTypes::EMAIL) && strlen($returnVal) == 0) || 
            (($paramType == ParamTypes::INT || $paramType == ParamTypes::DOUBLE) && strlen($returnVal) == 0)) {
                $returnVal = self::INVALID;
            }

            if ($paramType == ParamTypes::STRING &&
            $returnVal != self::INVALID &&
            strlen($returnVal) == 0 && 
            $def['options']['options']['allow-empty'] === false) {
                $returnVal = self::INVALID;
            }
        }

        return $returnVal;
    }
    private static function _applyCustomFilterFunc($def, $toBeFiltered) {
        $arrToPass = [
            'original-value' => $toBeFiltered,
        ];
        $paramObj = $def['parameter'];

        if ($paramObj->applyBasicFilter() === true) {
            $arrToPass['basic-filter-result'] = self::_getBasicFilterResultForCustomFilter($def, $toBeFiltered);
        } else {
            $arrToPass['basic-filter-result'] = 'NOT_APLICABLE';
        }
        $filterFuncResult = call_user_func($def['options']['filter-func'],$arrToPass['original-value'], $arrToPass['basic-filter-result'],$paramObj);

        if ($filterFuncResult === null) {
            $returnVal = false;
        } else {
            $returnVal = $filterFuncResult;
        }

        if ($returnVal === false && $paramObj->getType() != ParamTypes::BOOL) {
            $returnVal = self::INVALID;
        }

        return $returnVal;
    }
    private function _applyJsonBasicFilter(Json $extraClean, $toBeFiltered, $def) {
        $paramObj = $def['parameter'];
        $paramType = $paramObj->getType();
        $name = $paramObj->getName();
        $toBeFilteredType = gettype($toBeFiltered);

        if ($toBeFilteredType == 'string') {
            $toBeFiltered = strip_tags($toBeFiltered);
        }

        if ($paramType == $toBeFilteredType || $toBeFilteredType == 'object' && $paramType == ParamTypes::JSON_OBJ) {
            if ($paramType == ParamTypes::BOOL) {
                $extraClean->addBoolean($name, $toBeFiltered);
            } else if ($paramType == ParamTypes::DOUBLE || $paramType == ParamTypes::INT) {
                $extraClean->addNumber($name, $toBeFiltered);
            } else if ($paramType == 'string') {
                $this->_cleanJsonStr($extraClean, $def, $toBeFiltered);
            } else if ($paramType == ParamTypes::ARR) {
                $extraClean->addArray($name, $this->_cleanJsonArray($toBeFiltered, true));
            } else if ($paramType == ParamTypes::JSON_OBJ) {
                if ($toBeFiltered instanceof Json) {
                    $extraClean->add($name, $toBeFiltered);
                } else {
                    $extraClean->add($name, null);
                }
            }
        } else {
            $extraClean->add($name, null);
        }
    }
    private function _checkExtracted($extraClean, $name, $defaultVal) {
        $extractedVal = $extraClean->get($name);

        if ($extractedVal === null) {
            if ($defaultVal !== null) {
                $extraClean->add($name, $defaultVal);
            } else {
                $extraClean->add($name, null);
            }
        }
    }
    private function _cleanJsonArray(array $arr, $applyBasicFiltering = false) {
        $cleanArr = [];

        foreach ($arr as $val) {
            $propType = gettype($val);

            if ($propType == 'string') {
                if ($applyBasicFiltering) {
                    $cleanArr[] = filter_var($val, FILTER_SANITIZE_STRING);
                } else {
                    $cleanArr[] = $val;
                }
            } else if ($propType == 'array') {
                $cleanArr[] = $this->_cleanJsonArray($val, $applyBasicFiltering);
            } else if ($propType == 'object' && $val instanceof Json) {
                $cleanArr[] = $this->_jsonBasicClean($val, $applyBasicFiltering);
            } else {
                $cleanArr[] = $val;
            }
        }

        return $cleanArr;
    }
    private function _cleanJsonStr($extraClean, $def, $toBeFiltered) {
        $name = $def['parameter']->getName();
        $extraClean->add($name, filter_var($toBeFiltered));

        foreach ($def['filters'] as $val) {
            $extraClean->add($name, filter_var($extraClean->get($name), $val, $def['options']));
        }
        $cleaned = $extraClean->get($name);

        if (strlen($cleaned) == 0 && $def['options']['options']['allow-empty'] === false) {
            $extraClean->add($name, null);
        }
    }
    /**
     * Converts a string to an array.
     * 
     * @param string|array $arr A string in the format '[3,"hello",4.8,"",44,...]'.
     * 
     * @return string|array If the string has valid array format, an array 
     * which contains the values is returned. If has invalid syntax, the 
     * method will return the string 'APIFilter::INVALID'.
     * 
     * @since 1.2.1
     */
    private static function _filterArray($arr) {
        if (gettype($arr) == 'array') {
            return $arr;
        }
        $array = filter_var($arr);
        $len = strlen($array);
        $retVal = self::INVALID;
        $arrayValues = [];

        if ($len >= 2 && ($array[0] == '[' && $array[$len - 1] == ']')) {
            $tmpArrValue = '';

            for ($x = 1 ; $x < $len - 1 ; $x++) {
                $char = $array[$x];

                if ($x + 1 == $len - 1) {
                    $tmpArrValue .= $char;
                    $number = self::checkIsNumber($tmpArrValue);
                    $numType = gettype($number);

                    if ($numType == 'integer' || $numType == 'double') {
                        $arrayValues[] = $number;
                        continue;
                    } else {
                        return $retVal;
                    }
                } else if ($char == '"' || $char == "'") {
                    $tmpArrValue = strtolower(trim($tmpArrValue));

                    if (strlen($tmpArrValue) != 0) {
                        if ($tmpArrValue == 'true') {
                            $arrayValues[] = true;
                        } else if ($tmpArrValue == 'false') {
                            $arrayValues[] = false;
                        } else if ($tmpArrValue == 'null') {
                            $arrayValues[] = null;
                        } else {
                            $number = self::checkIsNumber($tmpArrValue);
                            $numType = gettype($number);

                            if ($numType == 'integer' || $numType == 'double') {
                                $arrayValues[] = $number;
                                continue;
                            } else {
                                return $retVal;
                            }
                        }
                    } else {
                        $result = self::_parseStringFromArray($array, $x + 1, $len - 1, $char);

                        if ($result['parsed'] === true) {
                            $x = $result['end'];
                            $arrayValues[] = filter_var(strip_tags($result[ParamTypes::STRING]));
                            $tmpArrValue = '';
                            continue;
                        } else {
                            return $retVal;
                        }
                    }
                }

                if ($char == ',') {
                    $tmpArrValue = strtolower(trim($tmpArrValue));

                    if ($tmpArrValue == 'true') {
                        $arrayValues[] = true;
                    } else if ($tmpArrValue == 'false') {
                        $arrayValues[] = false;
                    } else if ($tmpArrValue == 'null') {
                        $arrayValues[] = null;
                    } else {
                        $number = self::checkIsNumber($tmpArrValue);
                        $numType = gettype($number);

                        if ($numType == 'integer' || $numType == 'double') {
                            $arrayValues[] = $number;
                        } else {
                            return $retVal;
                        }
                    }
                    $tmpArrValue = '';
                } else if ($x + 1 == $len - 1) {
                    $arrayValues[] = $tmpArrValue.$char;
                } else {
                    $tmpArrValue .= $char;
                }
            }
            $retVal = $arrayValues;
        }

        return $retVal;
    }
    /**
     * Returns the boolean value of given input.
     * 
     * @param type $boolean
     * 
     * @return boolean|string
     * 
     * @since 1.1
     */
    private static function _filterBoolean($boolean) {
        if (gettype($boolean) == 'boolean') {
            return $boolean;
        }

        $booleanLwr = strtolower(filter_var($boolean));
        $boolTypes = [
            't' => true,
            'f' => false,
            'yes' => true,
            'no' => false,
            '-1' => false,
            '1' => true,
            '0' => false,
            'true' => true,
            'false' => false,
            'on' => true,
            'off' => false,
            'y' => true,
            'n' => false,
            'ok' => true];

        if (isset($boolTypes[$booleanLwr])) {
            return $boolTypes[$booleanLwr];
        }

        return self::INVALID;
    }
    private static function _getBasicFilterResultForCustomFilter($def, $toBeFiltered) {
        if (gettype($toBeFiltered) == 'string') {
            $toBeFiltered = strip_tags($toBeFiltered);
        }
        $paramType = $def['parameter']->getType();

        if ($paramType == ParamTypes::BOOL) {
            $filteredValue = self::_filterBoolean(filter_var($toBeFiltered));
        } else if ($paramType == ParamTypes::ARR) {
            $filteredValue = self::_filterArray($toBeFiltered);
        } else {
            $filteredValue = filter_var($toBeFiltered);

            foreach ($def['filters'] as $val) {
                $filteredValue = filter_var($filteredValue, $val, $def['options']);
            }

            if ($filteredValue === false) {
                $filteredValue = self::INVALID;
            }

            if ($paramType == ParamTypes::STRING &&
            $filteredValue != self::INVALID &&
            strlen($filteredValue) == 0 && 
            $def['options']['options']['allow-empty'] === false) {
                $filteredValue = self::INVALID;
            }
        }

        return $filteredValue;
    }
    private function _getJsonPropArr($arr, $propName) {
        $retVal = null;

        foreach ($arr as $val) {
            if ($val instanceof Json) {
                $retVal = $this->_getJsonPropVal($val, $propName);
            } else if (gettype($val) == 'array') {
                $retVal = $this->_getJsonPropArr($val, $propName);
            }

            if ($retVal !== null) {
                return $retVal;
            }
        }
    }
    private function _getJsonPropVal(Json $jsonx, $propName) {
        $propVal = $jsonx->get($propName);

        if ($propVal === null) {
            $props = $jsonx->getPropsNames();

            foreach ($props as $propNameX) {
                $testVal = $jsonx->get($propNameX);

                if ($testVal instanceof Json) {
                    $propVal = $this->_getJsonPropVal($testVal, $propName);
                } else if (gettype($testVal) == 'array') {
                    $propVal = $this->_getJsonPropArr($testVal, $propName);
                }

                if ($propVal !== null) {
                    return $propVal;
                }
            }
        }

        return $propVal;
    }
    private function _jsonBasicClean(Json $val, $applyBasicFiltering) {
        $cleanJson = new Json();

        foreach ($val->getPropsNames() as $propName) {
            $propVal = $val->get($propName);
            $propType = gettype($propVal);

            if ($propType == 'array') {
                $cleanJson->add($propName, $this->_cleanJsonArray($propVal, $applyBasicFiltering));
            } else if ($propType == 'object') {
                $cleanJson->add($propName, $this->_jsonBasicClean($propVal, $applyBasicFiltering));
            } else if ($propType == 'string') {
                if ($applyBasicFiltering) {
                    $cleanJson->add($propName, filter_var($propVal, FILTER_SANITIZE_STRING));
                } else {
                    $cleanJson->add($propName, $propVal);
                }
            } else {
                $cleanJson->add($propName, $propVal);
            }
        }

        return $cleanJson;
    }
    private function _jsonBody() {
        if ($this->inputStreamPath !== null) {
            $body = file_get_contents($this->inputStreamPath);
        } else {
            $body = file_get_contents('php://input');
        }
        $json = Json::decode($body);

        if (!($json instanceof Json)) {
            throw new Exception('Request body does not contain valid JSON.');
        }
        $this->filterJson($json);
    }
    /**
     * Extract string value from an array that is formed as string.
     * 
     * It is a helper method that works with the method APIFilter::_parseStringFromArray().
     * 
     * @param type $arr
     * 
     * @param type $start
     * 
     * @param type $len
     * 
     * @return boolean
     * 
     * @since 1.2.1
     */
    private static function _parseStringFromArray($arr,$start,$len,$stringEndChar) {
        $retVal = [
            'end' => 0,
            'string' => '',
            'parsed' => false
        ];
        $str = "";

        for ($x = $start ; $x < $len ; $x++) {
            $ch = $arr[$x];

            if ($ch == $stringEndChar) {
                $str .= "";
                $retVal['end'] = $x;
                $retVal['string'] = urldecode($str);
                $retVal['parsed'] = true;
                break;
            } else if ($ch == '\\') {
                $x++;
                $nextCh = $arr[$x];

                if ($ch != ' ') {
                    $str .= '\\'.$nextCh;
                } else {
                    $str .= '\\ ';
                }
            } else {
                $str .= $ch;
            }
        }

        for ($x = $retVal['end'] + 1 ; $x < $len ; $x++) {
            $ch = $arr[$x];

            if ($ch == ',') {
                $retVal['parsed'] = true;
                $retVal['end'] = $x;
                break;
            } else if ($ch != ' ') {
                $retVal['parsed'] = false;
                break;
            }
        }

        return $retVal;
    }
    /**
     * Checks if a given string represents an integer or float value. 
     * 
     * If the given string represents numeric value, the method will 
     * convert it to its numerical value.
     * 
     * @param string $str A value such as '1' or '7.0'.
     * 
     * @return string|int|double If the given string does not represents any 
     * numerical value, the method will return the string 'APIFilter::INVALID'. If the 
     * given string represents an integer, an integer value is returned. 
     * If the given string represents a floating point value, a float number 
     * is returned.
     */
    private static function checkIsNumber($str) {
        $strX = trim($str);
        $len = strlen($strX);
        $isFloat = false;
        $retVal = self::INVALID;

        for ($y = 0 ; $y < $len ; $y++) {
            $char = $strX[$y];

            if ($char == '.' && !$isFloat) {
                $isFloat = true;
            } else if (!($char == '-' && $y == 0)) {
                if ($char == '.' && $isFloat) {
                    return $retVal;
                } else if (!($char <= '9' && $char >= '0')) {
                    return $retVal;
                }
            }
        }

        if ($isFloat) {
            $retVal = floatval($strX);
        } else {
            $retVal = intval($strX);
        }

        return $retVal;
    }
    /**
     * 
     * @param Json $jsonx
     */
    private function filterJson(Json $cleanJson) {
        $originalInputs = new Json();
        $extraClean = new Json();
        $filterDef = $this->getFilterDef();
        $paramIdx = 'parameter';
        $optIdx = 'options';

        foreach ($filterDef as $def) {
            $requParam = $def[$paramIdx];
            $name = $requParam->getName();
            $paramType = $requParam->getType();
            $defaultVal = $requParam->getDefault();
            $requParamVal = $this->_getJsonPropVal($cleanJson, $name);

            if ($requParamVal !== null) {
                $toBeFiltered = $requParamVal;
                $originalInputs->add($name, $toBeFiltered);

                if (isset($def[$optIdx]['filter-func'])) {
                    $filteredValue = self::_applyCustomFilterFunc($def, $toBeFiltered);

                    if ($paramType == ParamTypes::STRING &&
                        $filteredValue != self::INVALID &&
                        strlen($filteredValue) == 0 && 
                        $def[$optIdx][$optIdx]['allow-empty'] === false) {
                        //Empty not allowed while custom filter function
                        //returned empty string
                        $filteredValue = null;
                    }
                    $extraClean->add($name, $filteredValue);
                    continue;
                } else {
                    self::_applyJsonBasicFilter($extraClean, $toBeFiltered, $def);
                }
                $this->_checkExtracted($extraClean, $name, $defaultVal);
            } else if ($requParam->isOptional()) {
                $defaultVal !== null ? $extraClean->add($name, $defaultVal) : $extraClean->add($name, null);
            }
        }
        $this->inputs = $extraClean;
        $this->nonFilteredInputs = $originalInputs;
    }
    private function setInputStreamHelper($trimmed, $mode) {
        $tempStream = fopen($trimmed, $mode);

        if (is_resource($tempStream)) {
            $this->inputStreamPath = $trimmed;

            return true;
        }

        return false;
    }
}
