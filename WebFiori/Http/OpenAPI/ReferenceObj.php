<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Reference Object in OpenAPI specification.
 * 
 * A simple object to allow referencing other components in the OpenAPI Description,
 * internally and externally.
 */
class ReferenceObj implements JsonI {
    private string $ref;
    private ?string $summary = null;
    private ?string $description = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $ref The reference identifier. This MUST be in the form of a URI. REQUIRED.
     */
    public function __construct(string $ref) {
        $this->setRef($ref);
    }
    
    /**
     * Sets the reference identifier.
     * 
     * @param string $ref The reference identifier. This MUST be in the form of a URI.
     * 
     * @return ReferenceObj
     */
    public function setRef(string $ref): ReferenceObj {
        $this->ref = $ref;
        return $this;
    }
    
    /**
     * Returns the reference identifier.
     * 
     * @return string
     */
    public function getRef(): string {
        return $this->ref;
    }
    
    /**
     * Sets a short summary which by default SHOULD override that of the referenced component.
     * 
     * @param string $summary A short summary.
     * 
     * @return ReferenceObj
     */
    public function setSummary(string $summary): ReferenceObj {
        $this->summary = $summary;
        return $this;
    }
    
    /**
     * Returns the summary.
     * 
     * @return string|null
     */
    public function getSummary(): ?string {
        return $this->summary;
    }
    
    /**
     * Sets a description which by default SHOULD override that of the referenced component.
     * 
     * @param string $description A description.
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @return ReferenceObj
     */
    public function setDescription(string $description): ReferenceObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description.
     * 
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Returns a Json object that represents the Reference Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('$ref', $this->ref);
        
        if ($this->summary !== null) {
            $json->add('summary', $this->summary);
        }
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        return $json;
    }
}
