<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Paths Object in OpenAPI specification.
 * 
 * Holds the relative paths to the individual endpoints and their operations.
 * The path is appended to the URL from the Server Object in order to construct the full URL.
 * The Paths Object MAY be empty, due to Access Control List (ACL) constraints.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#paths-object
 */
class PathsObj implements JsonI {
    /**
     * Map of path strings to Path Item Objects.
     * 
     * @var array
     */
    private array $paths = [];
    
    /**
     * Adds a path and its operations to the Paths Object.
     * 
     * The path field name MUST begin with a forward slash (/).
     * The path is appended to the expanded URL from the Server Object's url field.
     * 
     * @param string $path The relative path (must start with /). Example: "/users/{id}".
     * @param PathItemObj $pathItem The Path Item Object describing operations on this path.
     * 
     * @return PathsObj Returns self for method chaining.
     */
    public function addPath(string $path, PathItemObj $pathItem): PathsObj {
        $this->paths[$path] = $pathItem;
        return $this;
    }
    
    /**
     * Returns all paths and their operations.
     * 
     * @return array Map of path strings to Path Item Objects.
     */
    public function getPaths(): array {
        return $this->paths;
    }
    
    /**
     * Returns a Json object that represents the Paths Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        foreach ($this->paths as $path => $pathItem) {
            $json->add($path, $pathItem);
        }
        return $json;
    }
}
