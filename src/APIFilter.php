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
namespace restEasy;

/**
 * A class used to filter request parameters.
 * This class is the core class which is used to manage and set request 
 * parameters.
 * @author Ibrahim
 * @version 1.2.2
 */
class APIFilter {
    /**
     * A constant that indicates a given value is invalid.
     * @var string A string that indicates a given value is invalid.
     * @since 1.2.2
     */
    const INVALID = 'INV';
    /**
     * Supported input types.
     * The filter supports the following data types:
     * <ul>
     * <li>string</li>
     * <li>integer</li>
     * <li>email</li>
     * <li>float</li>
     * <li>url</li>
     * <li>boolean</li>
     * <li>array</li>
     * </ul>
     * @var array 
     * @since 1.0
     */
    const TYPES = [
        'string',
        'integer',
        'email',
        'float',
        'url',
        'boolean',
        'array'
    ];
    /**
     * An array that will contains filtered data.
     * @var array
     * @since 1.0 
     */
    private $inputs = [];
    /**
     * An array that contains non-filtered data (original).
     * @var array
     * @since 1.2 
     */
    private $nonFilteredInputs = [];
    /**
     * Array that contains filter definitions.
     * @var array
     * @since 1.0 
     */
    private $paramDefs = [];
    /**
     * Adds a new request parameter to the filter.
     * @param RequestParameter $reqParam The request parameter that will be added.
     * @since 1.1
     */
    public function addRequestParameter($reqParam) {
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

            if ($paramType == self::TYPES[1]) {
                if ($reqParam->getMaxVal() !== null) {
                    $attribute[$optIdx][$optIdx]['max_range'] = $reqParam->getMaxVal();
                }

                if ($reqParam->getMinVal() !== null) {
                    $attribute[$optIdx][$optIdx]['min_range'] = $reqParam->getMinVal();
                }
                array_push($attribute[$filterIdx], FILTER_SANITIZE_NUMBER_INT);
                array_push($attribute[$filterIdx], FILTER_VALIDATE_INT);
            } else if ($paramType == self::TYPES[0]) {
                $attribute[$optIdx][$optIdx]['allow-empty'] = $reqParam->isEmptyStringAllowed();
                array_push($attribute[$filterIdx], FILTER_DEFAULT);
            } else if ($paramType == self::TYPES[3]) {
                array_push($attribute[$filterIdx], FILTER_SANITIZE_NUMBER_FLOAT);
            } else if ($paramType == self::TYPES[2]) {
                array_push($attribute[$filterIdx], FILTER_SANITIZE_EMAIL);
                array_push($attribute[$filterIdx], FILTER_VALIDATE_EMAIL);
            } else if ($paramType == self::TYPES[4]) {
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
     * @since 1.2.2
     */
    public function clearInputs() {
        $this->inputs = [];
        $this->nonFilteredInputs = [];
    }
    /**
     * Clears filter parameters. 
     * @since 1.1
     */
    public function clearParametersDef() {
        $this->paramDefs = [];
    }
    /**
     * Filter the values of an associative array.
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
     * @param APIFilter $apiFilter An instance of the class 'APIFilter' that 
     * contains filter constrains.
     * @param array $arr An associative array of values which will be filtered.
     * @return array The method will return an associative array which has two 
     * indices. The index with key 'filtered' will contain an array which 
     * has all values filtered. The index which has the key 'non-filtered' 
     * will contain the original values.
     * @since 1.2.2
     */
    public static function filter($apiFilter,$arr) {
        $filteredIdx = 'filtered';
        $noFIdx = 'non-filtered';
        $retVal = [
            $filteredIdx => [],
            $noFIdx => []
        ];
        $paramIdx = 'parameter';
        $filterIdx = 'filters';
        $optIdx = 'options';

        if ($apiFilter instanceof APIFilter && gettype($arr) == self::TYPES[6]) {
            $filterDef = $apiFilter->getFilterDef();

            foreach ($filterDef as $def) {
                $name = $def[$paramIdx]->getName();
                $paramType = $def[$paramIdx]->getType();
                $defaultVal = $def[$paramIdx]->getDefault();

                if (isset($arr[$name])) {
                    $toBeFiltered = $arr[$name];
                    $retVal[$noFIdx][$name] = $arr[$name];

                    if (isset($def[$optIdx]['filter-func'])) {
                        $filteredValue = '';
                        $arrToPass = [
                            'original-value' => $toBeFiltered,
                        ];

                        if ($def[$paramIdx]->applyBasicFilter() === true) {
                            $toBeFiltered = strip_tags($toBeFiltered);

                            if ($paramType == self::TYPES[5]) {
                                $filteredValue = self::_filterBoolean(filter_var($toBeFiltered));
                            } else if ($paramType == self::TYPES[6]) {
                                $filteredValue = self::_filterArray(filter_var($toBeFiltered));
                            } else {
                                $filteredValue = filter_var($toBeFiltered);

                                foreach ($def[$filterIdx] as $val) {
                                    $filteredValue = filter_var($filteredValue, $val, $def[$optIdx]);
                                }

                                if ($filteredValue === false) {
                                    $filteredValue = self::INVALID;
                                }

                                if ($paramType == self::TYPES[0] &&
                                    $filteredValue != self::INVALID &&
                                    strlen($filteredValue) == 0 && 
                                    $def[$optIdx][$optIdx]['allow-empty'] === false) {
                                    $retVal[$filteredIdx][$name] = self::INVALID;
                                }
                            }
                            $arrToPass['basic-filter-result'] = $filteredValue;
                        } else {
                            $arrToPass['basic-filter-result'] = 'NOT_APLICABLE';
                        }
                        $r = call_user_func($def[$optIdx]['filter-func'],$arrToPass,$def[$paramIdx]);

                        if ($r === null) {
                            $retVal[$filteredIdx][$name] = false;
                        } else {
                            $retVal[$filteredIdx][$name] = $r;
                        }

                        if ($retVal[$filteredIdx][$name] === false && $paramType != self::TYPES[5]) {
                            $retVal[$filteredIdx][$name] = self::INVALID;
                        }
                    } else {
                        $toBeFiltered = strip_tags($toBeFiltered);

                        if ($paramType == self::TYPES[5]) {
                            $retVal[$filteredIdx][$name] = self::_filterBoolean(filter_var($toBeFiltered));
                        } else if ($paramType == self::TYPES[6]) {
                            $retVal[$filteredIdx][$name] = self::_filterArray(filter_var($toBeFiltered));
                        } else {
                            $retVal[$filteredIdx][$name] = filter_var($toBeFiltered);

                            foreach ($def[$filterIdx] as $val) {
                                $retVal[$filteredIdx][$name] = filter_var($retVal[$filteredIdx][$name], $val, $def[$optIdx]);
                            }

                            if ($retVal[$filteredIdx][$name] === false || 
                                (($paramType == self::TYPES[1] || $paramType == self::TYPES[3]) && strlen($retVal[$filteredIdx][$name]) == 0)) {
                                $retVal[$filteredIdx][$name] = self::INVALID;
                            }

                            if ($paramType == self::TYPES[0] &&
                                $retVal[$filteredIdx][$name] != self::INVALID &&
                                strlen($retVal[$filteredIdx][$name]) == 0 && 
                                $def[$optIdx][$optIdx]['allow-empty'] === false) {
                                $retVal[$filteredIdx][$name] = self::INVALID;
                            }
                        }
                    }

                    if ($retVal[$filteredIdx][$name] == self::INVALID && $defaultVal !== null) {
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
        }

        return $retVal;
    }
    /**
     * Filter GET parameters.
     * GET parameters are usually sent when request method is GET or DELETE.
     * The filtering algorithm will work as follows:
     * <ul>
     * <li>First, check if $_GET['param-name'] is set.</li>
     * <li>If not set, check if its optional. If optional and default value is 
     * given, then use default value. Else, set the filtered value to null.</li>
     * <li>If $_GET['param-name'] is given, then do the following steps:
     * <ul>
     * <li>First, apply basic filtering (if applicable).</li>
     * <li>If custom filter is provided, then apply it.</li>
     * </ul>
     * </li>
     * </ul>
     * @since 1.0
     */
    public final function filterGET() {
        $this->clearInputs();
        $filterResult = $this->filter($this, $_GET);
        $this->inputs = $filterResult['filtered'];
        $this->nonFilteredInputs = $filterResult['non-filtered'];
    }
    /**
     * Filter POST parameters.
     * POST parameters are usually sent when request method is POST or PUT.
     * @since 1.0
     */
    public final function filterPOST() {
        $this->clearInputs();
        $filterResult = $this->filter($this, $_POST);
        $this->inputs = $filterResult['filtered'];
        $this->nonFilteredInputs = $filterResult['non-filtered'];
    }
    /**
     * Returns an array that contains filter constraints.
     * @return array An array that contains filter constraints.
     * @since 1.2.2
     */
    public function getFilterDef() {
        return $this->paramDefs;
    }
    /**
     * Returns an associative array that contains request body inputs.
     * The data in the array will have the filters applied to.
     * @return array|null The array that contains request inputs. If no data was 
     * filtered, the method will return null.
     * @since 1.0
     */
    public function getInputs() {
        return $this->inputs;
    }
    /**
     * Returns the array that contains request inputs without filters applied.
     * @return array The array that contains request inputs.
     * @since 1.2
     */
    public final function getNonFiltered() {
        return $this->nonFilteredInputs;
    }
    /**
     * Converts a string to an array.
     * @param string $array A string in the format '[3,"hello",4.8,"",44,...]'.
     * @return string|array If the string has valid array format, an array 
     * which contains the values is returned. If has invalid syntax, the 
     * method will return the string 'APIFilter::INVALID'.
     * @since 1.2.1
     */
    private static function _filterArray($array) {
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

                    if ($number != self::INVALID) {
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

                            if ($number != self::INVALID) {
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
                            $arrayValues[] = filter_var(strip_tags($result[self::TYPES[0]]));
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

                        if ($number != self::INVALID) {
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
     * @param type $boolean
     * @return boolean|string
     * @since 1.1
     */
    private static function _filterBoolean($boolean) {
        $booleanLwr = strtolower($boolean);
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
    /**
     * Extract string value from an array that is formed as string.
     * It is a helper method that works with the method APIFilter::_parseStringFromArray().
     * @param type $arr
     * @param type $start
     * @param type $len
     * @return boolean
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
                $retVal['string'] = $str;
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
     * If the given string represents numeric value, the method will 
     * convert it to its numerical value.
     * @param string $str A value such as '1' or '7.0'.
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
}
