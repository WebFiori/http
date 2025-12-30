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
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Responses Object in OpenAPI specification.
 * 
 * A container for the expected responses of an operation.
 * The container maps a HTTP response code to the expected response.
 * 
 * The Responses Object MUST contain at least one response code, and if only one
 * response code is provided it SHOULD be the response for a successful operation call.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#responses-object
 */
class ResponsesObj implements JsonI {
    /**
     * Map of HTTP status codes to Response Objects.
     * 
     * @var array
     */
    private array $responses = [];
    
    /**
     * Adds a response for a specific HTTP status code.
     * 
     * Any HTTP status code can be used as the property name, but only one property per code.
     * The status code can also be a range using uppercase wildcard character X (e.g., "2XX").
     * 
     * @param string $statusCode The HTTP status code (e.g., "200", "404", "2XX").
     * @param ResponseObj|string $response The Response Object or description string for this status code.
     * 
     * @return ResponsesObj Returns self for method chaining.
     */
    public function addResponse(string $statusCode, ResponseObj|string $response): ResponsesObj {
        if (is_string($response)) {
            $response = new ResponseObj($response);
        }
        $this->responses[$statusCode] = $response;
        return $this;
    }
    
    /**
     * Returns all responses mapped by status code.
     * 
     * @return array Map of status codes to Response Objects.
     */
    public function getResponses(): array {
        return $this->responses;
    }
    
    /**
     * Returns a Json object that represents the Responses Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        foreach ($this->responses as $code => $response) {
            $json->add($code, $response);
        }
        
        return $json;
    }
}
