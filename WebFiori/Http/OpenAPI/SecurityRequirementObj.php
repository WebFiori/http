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
 * Represents a Security Requirement Object in OpenAPI specification.
 * 
 * Lists the required security schemes to execute this operation.
 * The object can have multiple security schemes declared in it which are all required 
 * (that is, there is a logical AND between the schemes).
 * 
 * When a list of Security Requirement Objects is defined on the OpenAPI Object or 
 * Operation Object, only one of the Security Requirement Objects in the list needs 
 * to be satisfied to authorize the request (their relationship is logical OR).
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#security-requirement-object
 */
class SecurityRequirementObj implements JsonI {
    /**
     * Each named security scheme MUST correspond to a security scheme declared in the Security Schemes.
     * 
     * If the security scheme is of type "oauth2" or "openIdConnect", then the value is a list 
     * of scope names required for the execution, but will be empty for other security scheme types.
     * 
     * @var array
     */
    private array $requirements = [];

    /**
     * Adds a security requirement with optional scopes.
     * 
     * The name must correspond to a security scheme declared in the Components Object.
     * For oauth2 and openIdConnect, provide an array of required scope names.
     * For other security scheme types, use an empty array.
     * 
     * @param string $name The name of the security scheme (must match a scheme in components).
     * @param array $scopes Array of scope names required (for oauth2/openIdConnect only).
     * 
     * @return SecurityRequirementObj Returns self for method chaining.
     */
    public function addRequirement(string $name, array $scopes = []): SecurityRequirementObj {
        $this->requirements[$name] = $scopes;

        return $this;
    }

    /**
     * Returns all security requirements.
     * 
     * @return array Map of security scheme names to scope arrays.
     */
    public function getRequirements(): array {
        return $this->requirements;
    }

    /**
     * Returns a Json object that represents the Security Requirement Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();

        foreach ($this->requirements as $name => $scopes) {
            $json->add($name, $scopes);
        }

        return $json;
    }
}
