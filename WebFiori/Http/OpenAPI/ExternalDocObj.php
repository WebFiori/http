<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an External Documentation Object in OpenAPI specification.
 * 
 * Allows referencing an external resource for extended documentation.
 */
class ExternalDocObj implements JsonI {
    private ?string $description = null;
    private string $url;
    
    /**
     * Creates new instance.
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
     * @param string $description A description of the target documentation.
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @return ExternalDocObj
     */
    public function setDescription(string $description): ExternalDocObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description of the target documentation.
     * 
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets the URI for the target documentation.
     * 
     * @param string $url The URI for the target documentation. This MUST be in the form of a URI.
     * 
     * @return ExternalDocObj
     */
    public function setUrl(string $url): ExternalDocObj {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Returns the URI for the target documentation.
     * 
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }
    
    /**
     * Returns a Json object that represents the External Documentation Object.
     * 
     * @return Json
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
