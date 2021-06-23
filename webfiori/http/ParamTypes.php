<?php
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
class ParamTypes {
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
     * Returns an array that contains all supported parameters types.
     * 
     * @return array An array that contains all supported parameters types.
     * 
     * @since 1.0
     */
    public static function getTypes() {
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
