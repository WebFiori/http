<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Tag Object in OpenAPI specification.
 * 
 * Adds metadata to a single tag that is used by the Operation Object.
 * It is not mandatory to have a Tag Object per tag defined in the Operation Object instances.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#tag-object
 */
class TagObj implements JsonI {
    /**
     * The name of the tag.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $name;
    
    /**
     * A description for the tag.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @var string|null
     */
    private ?string $description = null;
    
    /**
     * Additional external documentation for this tag.
     * 
     * @var ExternalDocObj|null
     */
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
     * The tag name is used to group operations in the OpenAPI Description.
     * 
     * @param string $name The name of the tag.
     * 
     * @return TagObj Returns self for method chaining.
     */
    public function setName(string $name): TagObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the name of the tag.
     * 
     * @return string The tag name.
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * Sets the description for the tag.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @param string $description A description for the tag.
     * 
     * @return TagObj Returns self for method chaining.
     */
    public function setDescription(string $description): TagObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description for the tag.
     * 
     * @return string|null The description, or null if not set.
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets additional external documentation for this tag.
     * 
     * @param ExternalDocObj $externalDocs External documentation object.
     * 
     * @return TagObj Returns self for method chaining.
     */
    public function setExternalDocs(ExternalDocObj $externalDocs): TagObj {
        $this->externalDocs = $externalDocs;
        return $this;
    }
    
    /**
     * Returns the external documentation for this tag.
     * 
     * @return ExternalDocObj|null The external documentation object, or null if not set.
     */
    public function getExternalDocs(): ?ExternalDocObj {
        return $this->externalDocs;
    }
    
    /**
     * Returns a Json object that represents the Tag Object.
     * 
     * The JSON structure follows the OpenAPI 3.1.0 specification.
     * 
     * @return Json A Json object representation of this Tag Object.
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
