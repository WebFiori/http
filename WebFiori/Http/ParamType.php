<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * A class that contains constants for representing request parameters types.
 *
 * @author Ibrahim
 * 
 * 
 */
class ParamType {
    /**
     * A constant to indicate that a parameter is of type array.
     * 
     */
    const ARR = 'array';
    /**
     * A constant to indicate that a parameter is of type boolean.
     * 
     */
    const BOOL = 'boolean';
    /**
     * A constant to indicate that a parameter is of type float or double.
     * 
     */
    const DOUBLE = 'double';
    /**
     * A constant to indicate that a parameter is of type email.
     * 
     */
    const EMAIL = 'email';
    /**
     * A constant to indicate that a parameter is of type integer.
     * 
     */
    const INT = 'integer';
    /**
     * A constant to indicate that a parameter is of type JSON object.
     * 
     */
    const JSON_OBJ = 'json-obj';
    /**
     * A constant to indicate that a parameter is of type string.
     * 
     */
    const STRING = 'string';
    /**
     * A constant to indicate that a parameter is of type url.
     * 
     */
    const URL = 'url';
    /**
     * Returns an array that contains names of types which are considered as
     * numeric.
     * 
     * @return array An array that contains names of types which are considered as
     * numeric.
     */
    public static function getNumericTypes() : array {
        return [
            self::DOUBLE, 
            self::INT, 
        ];
    }
    /**
     * Returns an array that contains names of types which are considered as
     * string.
     * 
     * @return array An array that contains names of types which are considered as
     * string.
     */
    public static function getStringTypes() : array {
        return [
            self::EMAIL, 
            self::STRING,
            self::URL
        ];
    }
    /**
     * Returns an array that contains all supported parameters types.
     * 
     * @return array An array that contains all supported parameters types.
     * 
     */
    public static function getTypes() : array {
        return [
            self::ARR, 
            self::BOOL, 
            self::EMAIL, 
            self::DOUBLE, 
            self::INT, 
            self::JSON_OBJ, 
            self::STRING,
            self::URL
        ];
    }
}
