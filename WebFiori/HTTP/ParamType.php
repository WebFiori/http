<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace webfiori\http;

/**
 * A class that contains constants for representing request parameters types.
 *
 * @author Ibrahim
 * 
 * @version 1.0
 * 
 * @since 1.5.2
 */
class ParamType {
    /**
     * A constant to indicate that a parameter is of type array.
     * 
     * @since 1.0
     */
    const ARR = 'array';
    /**
     * A constant to indicate that a parameter is of type boolean.
     * 
     * @since 1.0
     */
    const BOOL = 'boolean';
    /**
     * A constant to indicate that a parameter is of type float or double.
     * 
     * @since 1.0
     */
    const DOUBLE = 'double';
    /**
     * A constant to indicate that a parameter is of type email.
     * 
     * @since 1.0
     */
    const EMAIL = 'email';
    /**
     * A constant to indicate that a parameter is of type integer.
     * 
     * @since 1.0
     */
    const INT = 'integer';
    /**
     * A constant to indicate that a parameter is of type JSON object.
     * 
     * @since 1.0
     */
    const JSON_OBJ = 'json-obj';
    /**
     * A constant to indicate that a parameter is of type string.
     * 
     * @since 1.0
     */
    const STRING = 'string';
    /**
     * A constant to indicate that a parameter is of type url.
     * 
     * @since 1.0
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
     * @since 1.0
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
