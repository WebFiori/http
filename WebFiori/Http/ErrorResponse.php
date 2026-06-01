<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use WebFiori\Json\Json;

/**
 * A helper class for generating standardized JSON error responses.
 * 
 * Each method returns a Json object containing the error response body
 * along with the appropriate HTTP status code.
 *
 * @author Ibrahim
 */
class ErrorResponse {
    /**
     * Generates a 415 content type not supported response.
     * 
     * @param string $type The unsupported content type.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function contentTypeNotSupported(string $type = '') : array {
        $json = new Json();
        $json->add('message', ResponseMessage::get('415'));
        $json->add('type', WebService::E);
        $json->add('http-code', 415);

        if (!empty($type)) {
            $json->add('more-info', new Json(['request-content-type' => $type]));
        }

        return ['json' => $json, 'code' => 415];
    }
    /**
     * Generates a response for invalid parameters.
     * 
     * @param array $params Array of parameter names that have invalid values.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function invalidParams(array $params) : array {
        $val = self::formatParamNames($params);
        $json = new Json();
        $json->add('message', ResponseMessage::get('404-1').$val.'.');
        $json->add('type', WebService::E);
        $json->add('http-code', 404);
        $json->add('more-info', new Json(['invalid' => $params]));

        return ['json' => $json, 'code' => 404];
    }
    /**
     * Generates a 405 method not allowed response.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function methodNotAllowed() : array {
        $json = new Json();
        $json->add('message', ResponseMessage::get('405'));
        $json->add('type', WebService::E);
        $json->add('http-code', 405);

        return ['json' => $json, 'code' => 405];
    }
    /**
     * Generates a response for missing required parameters.
     * 
     * @param array $params Array of parameter names that are missing.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function missingParams(array $params) : array {
        $val = self::formatParamNames($params);
        $json = new Json();
        $json->add('message', ResponseMessage::get('404-2').$val.'.');
        $json->add('type', WebService::E);
        $json->add('http-code', 404);
        $json->add('more-info', new Json(['missing' => $params]));

        return ['json' => $json, 'code' => 404];
    }
    /**
     * Generates a 404 response for missing service name.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function missingServiceName() : array {
        $json = new Json();
        $json->add('message', ResponseMessage::get('404-3'));
        $json->add('type', WebService::E);
        $json->add('http-code', 404);

        return ['json' => $json, 'code' => 404];
    }
    /**
     * Generates a 404 response for unsupported service.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function serviceNotFound() : array {
        $json = new Json();
        $json->add('message', ResponseMessage::get('404-5'));
        $json->add('type', WebService::E);
        $json->add('http-code', 404);

        return ['json' => $json, 'code' => 404];
    }
    /**
     * Generates a 404 response for unimplemented service.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function serviceNotImplemented() : array {
        $json = new Json();
        $json->add('message', ResponseMessage::get('404-4'));
        $json->add('type', WebService::E);
        $json->add('http-code', 404);

        return ['json' => $json, 'code' => 404];
    }
    /**
     * Generates a 401 unauthorized response.
     * 
     * @param string|null $message Custom denial message. If null, uses default.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function unauthorized(?string $message = null) : array {
        $msg = $message !== null ? $message : ResponseMessage::get('401');
        $json = new Json();
        $json->add('message', $msg);
        $json->add('type', WebService::E);
        $json->add('http-code', 401);

        return ['json' => $json, 'code' => 401];
    }
    /**
     * Generates a 406 Not Acceptable response.
     * 
     * @param array $supported The content types the server can produce.
     * 
     * @return array{json: Json, code: int} The response body and HTTP code.
     */
    public static function notAcceptable(array $supported = []) : array {
        $json = new Json();
        $json->add('message', 'Not Acceptable');
        $json->add('type', WebService::E);
        $json->add('http-code', 406);

        if (!empty($supported)) {
            $json->add('more-info', new Json(['supported' => $supported]));
        }

        return ['json' => $json, 'code' => 406];
    }
    /**
     * Formats an array of parameter names into a comma-separated quoted string.
     */
    private static function formatParamNames(array $params) : string {
        $val = '';
        $count = count($params);
        $i = 0;

        foreach ($params as $paramName) {
            if ($i + 1 == $count) {
                $val .= '\''.$paramName.'\'';
            } else {
                $val .= '\''.$paramName.'\', ';
            }
            $i++;
        }

        return $val;
    }
}
