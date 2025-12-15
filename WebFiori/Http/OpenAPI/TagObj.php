<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Tag Object in OpenAPI specification.
 * 
 * Adds metadata to a single tag that is used by the Operation Object.
 * It is not mandatory to have a Tag Object per tag defined in the Operation Object instances.
 */
class TagObj implements JsonI {
    private string $name;
    private ?string $description = null;
    private ?ExternalDocObj $externalDocs = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $name The name of the tag. REQUIRED.
     * @param string|null $description A description for the tag.
     */
    public function __construct(string $name, ?string $description = null) {
        $this->setName($name);
        
        if ($description !== null) {
            $this->setDescription($description);
        }
    }
    
    /**
     * Sets the name of the tag.
     * 
     * @param string $name The name of the tag.
     * 
     * @return TagObj
     */
    public function setName(string $name): TagObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the name of the tag.
     * 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * Sets the description for the tag.
     * 
     * @param string $description A description for the tag.
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @return TagObj
     */
    public function setDescription(string $description): TagObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description for the tag.
     * 
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets additional external documentation for this tag.
     * 
     * @param ExternalDocObj $externalDocs External documentation object.
     * 
     * @return TagObj
     */
    public function setExternalDocs(ExternalDocObj $externalDocs): TagObj {
        $this->externalDocs = $externalDocs;
        return $this;
    }
    
    /**
     * Returns the external documentation for this tag.
     * 
     * @return ExternalDocObj|null
     */
    public function getExternalDocs(): ?ExternalDocObj {
        return $this->externalDocs;
    }
    
    /**
     * Returns a Json object that represents the Tag Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('name', $this->name);
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        if ($this->externalDocs !== null) {
            $json->add('externalDocs', $this->externalDocs);
        }
        
        return $json;
    }
}
