<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an Operation Object in OpenAPI specification.
 * 
 * Describes a single API operation on a path.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#operation-object
 */
class OperationObj implements JsonI {
    /**
     * The list of possible responses as they are returned from executing this operation.
     * 
     * REQUIRED.
     * 
     * @var ResponsesObj
     */
    private ResponsesObj $responses;
    
    public function __construct(ResponsesObj $responses) {
        $this->responses = $responses;
    }
    
    public function setResponses(ResponsesObj $responses): OperationObj {
        $this->responses = $responses;
        return $this;
    }
    
    public function getResponses(): ResponsesObj {
        return $this->responses;
    }
    
    public function toJSON(): Json {
        $json = new Json();
        $json->add('responses', $this->responses);
        return $json;
    }
}
