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
 * Represents a Response Object in OpenAPI specification.
 * 
 * Describes a single response from an API operation, including design-time, 
 * static links to operations based on the response.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#response-object
 */
class ResponseObj implements JsonI {
    /**
     * A description of the response.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $description;

    public function __construct(string $description) {
        $this->description = $description;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): ResponseObj {
        $this->description = $description;

        return $this;
    }

    public function toJSON(): Json {
        $json = new Json([
            'description' => $this->getDescription()
        ]);

        return $json;
    }
}
