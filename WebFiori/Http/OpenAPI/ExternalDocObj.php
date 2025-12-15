<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an External Documentation Object in OpenAPI specification.
 * 
 * Allows referencing an external resource for extended documentation.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#external-documentation-object
 */
class ExternalDocObj implements JsonI {
    /**
     * A description of the target documentation.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @var string|null
     */
    private ?string $description = null;
    
    /**
     * The URI for the target documentation.
     * 
     * This MUST be in the form of a URI.
     * 
     * @var string
     */
    private string $url;
    
    /**
     * Creates new instance of External Documentation Object.
     * 
     * @param string $url The URI for the target documentation. REQUIRED.
     * @param string|null $description A description of the target documentation.
     */
    public function __construct(string $url, ?string $description = null) {
        $this->setUrl($url);
        
        if ($description !== null) {
            $this->setDescription($description);
        }
    }
    
    /**
     * Sets the description of the target documentation.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @param string $description A description of the target documentation.
     * 
     * @return ExternalDocObj Returns self for method chaining.
     */
    public function setDescription(string $description): ExternalDocObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description of the target documentation.
     * 
     * @return string|null The description, or null if not set.
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets the URI for the target documentation.
     * 
     * This MUST be in the form of a URI.
     * 
     * @param string $url The URI for the target documentation.
     * 
     * @return ExternalDocObj Returns self for method chaining.
     */
    public function setUrl(string $url): ExternalDocObj {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Returns the URI for the target documentation.
     * 
     * @return string The URI for the target documentation.
     */
    public function getUrl(): string {
        return $this->url;
    }
    
    /**
     * Returns a Json object that represents the External Documentation Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification.
     * 
     * @return Json A Json object representation of this External Documentation Object.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        $json->add('url', $this->url);
        
        return $json;
    }
}
