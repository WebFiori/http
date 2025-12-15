<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Header Object in OpenAPI specification.
 * 
 * The Header Object follows the structure of the Parameter Object with some differences.
 */
class HeaderObj implements JsonI {
    private ?string $description = null;
    private bool $required = false;
    private bool $deprecated = false;
    private ?string $style = null;
    private ?bool $explode = null;
    private $schema = null;
    private $example = null;
    private ?array $examples = null;
    
    /**
     * Sets the description of the header.
     * 
     * @param string $description A brief description of the header.
     * 
     * @return HeaderObj
     */
    public function setDescription(string $description): HeaderObj {
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
     * Sets whether this header is mandatory.
     * 
     * @param bool $required True if required.
     * 
     * @return HeaderObj
     */
    public function setRequired(bool $required): HeaderObj {
        $this->required = $required;
        return $this;
    }
    
    /**
     * Returns whether this header is required.
     * 
     * @return bool
     */
    public function isRequired(): bool {
        return $this->required;
    }
    
    /**
     * Sets whether this header is deprecated.
     * 
     * @param bool $deprecated True if deprecated.
     * 
     * @return HeaderObj
     */
    public function setDeprecated(bool $deprecated): HeaderObj {
        $this->deprecated = $deprecated;
        return $this;
    }
    
    /**
     * Returns whether this header is deprecated.
     * 
     * @return bool
     */
    public function isDeprecated(): bool {
        return $this->deprecated;
    }
    
    /**
     * Sets the serialization style.
     * 
     * @param string $style The style value. Default is "simple".
     * 
     * @return HeaderObj
     */
    public function setStyle(string $style): HeaderObj {
        $this->style = $style;
        return $this;
    }
    
    /**
     * Returns the style.
     * 
     * @return string|null
     */
    public function getStyle(): ?string {
        return $this->style;
    }
    
    /**
     * Sets the explode value.
     * 
     * @param bool $explode The explode value.
     * 
     * @return HeaderObj
     */
    public function setExplode(bool $explode): HeaderObj {
        $this->explode = $explode;
        return $this;
    }
    
    /**
     * Returns the explode value.
     * 
     * @return bool|null
     */
    public function getExplode(): ?bool {
        return $this->explode;
    }
    
    /**
     * Sets the schema defining the type used for the header.
     * 
     * @param mixed $schema Schema Object or any schema definition.
     * 
     * @return HeaderObj
     */
    public function setSchema($schema): HeaderObj {
        $this->schema = $schema;
        return $this;
    }
    
    /**
     * Returns the schema.
     * 
     * @return mixed
     */
    public function getSchema() {
        return $this->schema;
    }
    
    /**
     * Sets an example of the header's potential value.
     * 
     * @param mixed $example Example value.
     * 
     * @return HeaderObj
     */
    public function setExample($example): HeaderObj {
        $this->example = $example;
        return $this;
    }
    
    /**
     * Returns the example.
     * 
     * @return mixed
     */
    public function getExample() {
        return $this->example;
    }
    
    /**
     * Sets examples of the header's potential value.
     * 
     * @param array $examples Map of example names to Example Objects or Reference Objects.
     * 
     * @return HeaderObj
     */
    public function setExamples(array $examples): HeaderObj {
        $this->examples = $examples;
        return $this;
    }
    
    /**
     * Returns the examples.
     * 
     * @return array|null
     */
    public function getExamples(): ?array {
        return $this->examples;
    }
    
    /**
     * Returns a Json object that represents the Header Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        if ($this->required) {
            $json->add('required', $this->required);
        }
        
        if ($this->deprecated) {
            $json->add('deprecated', $this->deprecated);
        }
        
        if ($this->style !== null) {
            $json->add('style', $this->style);
        }
        
        if ($this->explode !== null) {
            $json->add('explode', $this->explode);
        }
        
        if ($this->schema !== null) {
            $json->add('schema', $this->schema);
        }
        
        if ($this->example !== null) {
            $json->add('example', $this->example);
        }
        
        if ($this->examples !== null) {
            $json->add('examples', $this->examples);
        }
        
        return $json;
    }
}
