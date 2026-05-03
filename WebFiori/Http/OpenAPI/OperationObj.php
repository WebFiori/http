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

    /**
     * A list of parameters that are applicable for this operation.
     * 
     * @var ParameterObj[]
     */
    private array $parameters = [];

    /**
     * The request body applicable for this operation.
     * 
     * @var Json|null
     */
    private ?Json $requestBody = null;

    public function __construct(ResponsesObj $responses) {
        $this->responses = $responses;
    }

    public function getResponses(): ResponsesObj {
        return $this->responses;
    }

    public function setResponses(ResponsesObj $responses): OperationObj {
        $this->responses = $responses;

        return $this;
    }

    /**
     * Adds a parameter to this operation.
     * 
     * @param ParameterObj $param The parameter to add.
     * 
     * @return OperationObj Returns self for method chaining.
     */
    public function addParameter(ParameterObj $param): OperationObj {
        $this->parameters[] = $param;

        return $this;
    }

    /**
     * Returns the parameters for this operation.
     * 
     * @return ParameterObj[]
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * Sets the request body for this operation.
     * 
     * @param Json $requestBody The request body object.
     * 
     * @return OperationObj Returns self for method chaining.
     */
    public function setRequestBody(Json $requestBody): OperationObj {
        $this->requestBody = $requestBody;

        return $this;
    }

    /**
     * Returns the request body.
     * 
     * @return Json|null
     */
    public function getRequestBody(): ?Json {
        return $this->requestBody;
    }

    public function toJSON(): Json {
        $json = new Json();

        if (!empty($this->parameters)) {
            $json->add('parameters', $this->parameters);
        }

        if ($this->requestBody !== null) {
            $json->add('requestBody', $this->requestBody);
        }

        $json->add('responses', $this->getResponses());

        return $json;
    }
}
