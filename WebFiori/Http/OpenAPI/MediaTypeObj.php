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
 * Represents a Media Type Object in OpenAPI specification.
 * 
 * Each Media Type Object provides schema and examples for the media type identified by its key.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#media-type-object
 */
class MediaTypeObj implements JsonI {
    /**
     * The schema defining the content of the request, response, parameter, or header.
     * 
     * @var mixed
     */
    private $schema = null;
    
    public function setSchema($schema): MediaTypeObj {
        $this->schema = $schema;
        return $this;
    }
    
    public function getSchema() {
        return $this->schema;
    }
    
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->getSchema() !== null) {
            $json->add('schema', $this->getSchema());
        }
        
        return $json;
    }
}
