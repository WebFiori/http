<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2025-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 * 
 */
namespace WebFiori\Http;

/**
 * A class which is used to hold names of request methods.
 *
 * @author Ibrahim
 */
class RequestMethod {
    /**
     * A constant which is used to represent 'connect' request method.
     * 
     * @var string
     */
    const CONNECT = 'CONNECT';
    /**
     * A constant which is used to represent 'delete' request method.
     * 
     * @var string
     */
    const DELETE = 'DELETE';
    /**
     * A constant which is used to represent 'GET' request method.
     * 
     * @var string
     */
    const GET = 'GET';
    /**
     * A constant which is used to represent 'head' request method.
     * 
     * @var string
     */
    const HEAD = 'HEAD';
    /**
     * A constant which is used to represent 'options' request method.
     * 
     * @var string
     */
    const OPTIONS = 'OPTIONS';
    /**
     * A constant which is used to represent 'patch' request method.
     * 
     * @var string
     */
    const PATCH = 'PATCH';
    /**
     * A constant which is used to represent 'GET' request method.
     * 
     * @var string
     */
    const POST = 'POST';
    /**
     * A constant which is used to represent 'PUT' request method.
     * 
     * @var string
     */
    const PUT = 'PUT';
    /**
     * A constant which is used to represent 'trace' request method.
     * 
     * @var string
     */
    const TRACE = 'TRACE';
    /**
     * Returns an array that holds request methods in upper case.
     * 
     * The returned array will contains the following strings:
     * <ul>
     * <li>GET</li>
     * <li>HEAD</li>
     * <li>POST</li>
     * <li>PUT</li>
     * <li>DELETE</li>
     * <li>TRACE</li>
     * <li>OPTIONS</li>
     * <li>PATCH</li>
     * <li>CONNECT</li>
     * </ul>
     * 
     * @return array An array of request methods names.
     */
    public static function getAll() : array {
        return [
            self::CONNECT,
            self::DELETE,
            self::GET,
            self::HEAD,
            self::OPTIONS,
            self::POST,
            self::PUT,
            self::TRACE,
            self::PATCH
        ];
    }
}
