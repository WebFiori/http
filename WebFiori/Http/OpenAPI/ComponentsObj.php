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
 * Represents a Components Object in OpenAPI specification.
 * 
 * Holds a set of reusable objects for different aspects of the OAS.
 * All objects defined within the Components Object will have no effect on the API 
 * unless they are explicitly referenced from outside the Components Object.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#components-object
 */
class ComponentsObj implements JsonI {
    /**
     * An object to hold reusable Schema Objects.
     * 
     * @var array
     */
    private array $schemas = [];
    
    /**
     * An object to hold reusable Security Scheme Objects.
     * 
     * @var array
     */
    private array $securitySchemes = [];
    
    /**
     * Adds a reusable Schema Object to the components.
     * 
     * The key MUST match the regular expression: ^[a-zA-Z0-9\.\-_]+$
     * 
     * @param string $name The name/key for the schema (e.g., "User", "ErrorModel").
     * @param mixed $schema The Schema Object or schema definition.
     * 
     * @return ComponentsObj Returns self for method chaining.
     */
    public function addSchema(string $name, $schema): ComponentsObj {
        $this->schemas[$name] = $schema;
        return $this;
    }
    
    /**
     * Adds a reusable Security Scheme Object to the components.
     * 
     * The key MUST match the regular expression: ^[a-zA-Z0-9\.\-_]+$
     * 
     * @param string $name The name/key for the security scheme (e.g., "bearerAuth", "apiKey").
     * @param SecuritySchemeObj $scheme The Security Scheme Object.
     * 
     * @return ComponentsObj Returns self for method chaining.
     */
    public function addSecurityScheme(string $name, SecuritySchemeObj $scheme): ComponentsObj {
        $this->securitySchemes[$name] = $scheme;
        return $this;
    }
    
    /**
     * Returns all schemas.
     * 
     * @return array Map of schema names to schema definitions.
     */
    public function getSchemas(): array {
        return $this->schemas;
    }
    
    /**
     * Returns all security schemes.
     * 
     * @return array Map of security scheme names to SecuritySchemeObj.
     */
    public function getSecuritySchemes(): array {
        return $this->securitySchemes;
    }
    
    /**
     * Returns a Json object that represents the Components Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if (!empty($this->schemas)) {
            $json->add('schemas', $this->schemas);
        }
        
        if (!empty($this->securitySchemes)) {
            $json->add('securitySchemes', $this->securitySchemes);
        }
        
        return $json;
    }
}
