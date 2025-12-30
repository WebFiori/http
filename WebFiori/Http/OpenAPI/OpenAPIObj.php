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
 * Represents the root OpenAPI Object in OpenAPI specification.
 * 
 * This is the root object of the OpenAPI Description.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#openapi-object
 */
class OpenAPIObj implements JsonI {
    /**
     * The version number of the OpenAPI Specification that the OpenAPI Document uses.
     * 
     * The openapi field SHOULD be used by tooling to interpret the OpenAPI Document.
     * This is not related to the API info.version string.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $openapi;
    
    /**
     * Provides metadata about the API.
     * 
     * The metadata MAY be used by tooling as required.
     * 
     * REQUIRED.
     * 
     * @var InfoObj
     */
    private InfoObj $info;
    
    /**
     * The available paths and operations for the API.
     * 
     * @var PathsObj|null
     */
    private ?PathsObj $paths = null;
    
    /**
     * Creates a new OpenAPI Object instance.
     * 
     * This represents the root of an OpenAPI Description document.
     * 
     * @param InfoObj $info Provides metadata about the API. REQUIRED.
     * @param string $openapi The OpenAPI Specification version. Defaults to '3.1.0'.
     */
    public function __construct(InfoObj $info, string $openapi = '3.1.0') {
        $this->info = $info;
        $this->openapi = $openapi;
    }
    
    /**
     * Sets the OpenAPI Specification version number.
     * 
     * This string MUST be the version number of the OpenAPI Specification 
     * that the OpenAPI Document uses (e.g., "3.1.0").
     * 
     * @param string $openapi The OpenAPI Specification version.
     * 
     * @return OpenAPIObj Returns self for method chaining.
     */
    public function setOpenapi(string $openapi): OpenAPIObj {
        $this->openapi = $openapi;
        return $this;
    }
    
    /**
     * Returns the OpenAPI Specification version number.
     * 
     * @return string The OpenAPI Specification version.
     */
    public function getOpenapi(): string {
        return $this->openapi;
    }
    
    /**
     * Sets the Info Object containing API metadata.
     * 
     * @param InfoObj $info The Info Object with API metadata.
     * 
     * @return OpenAPIObj Returns self for method chaining.
     */
    public function setInfo(InfoObj $info): OpenAPIObj {
        $this->info = $info;
        return $this;
    }
    
    /**
     * Returns the Info Object containing API metadata.
     * 
     * @return InfoObj The Info Object.
     */
    public function getInfo(): InfoObj {
        return $this->info;
    }
    
    /**
     * Sets the Paths Object containing API paths and operations.
     * 
     * @param PathsObj $paths The Paths Object.
     * 
     * @return OpenAPIObj Returns self for method chaining.
     */
    public function setPaths(PathsObj $paths): OpenAPIObj {
        $this->paths = $paths;
        return $this;
    }
    
    /**
     * Returns the Paths Object containing API paths and operations.
     * 
     * @return PathsObj|null The Paths Object or null if not set.
     */
    public function getPaths(): ?PathsObj {
        return $this->paths;
    }
    
    /**
     * Returns a Json object that represents the OpenAPI Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification and represents
     * a complete OpenAPI Description document.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json([
            'openapi' => $this->getOpenapi(),
            'info' => $this->getInfo()
        ]);
        
        if ($this->getPaths() !== null) {
            $json->add('paths', $this->getPaths());
        }
        
        return $json;
    }
}
