<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Parameter Object in OpenAPI specification.
 * 
 * Describes a single operation parameter.
 */
class ParameterObj implements JsonI {
    private string $name;
    private string $in;
    private ?string $description = null;
    private bool $required = false;
    private bool $deprecated = false;
    private bool $allowEmptyValue = false;
    private ?string $style = null;
    private ?bool $explode = null;
    private ?bool $allowReserved = null;
    private $schema = null;
    private $example = null;
    private ?array $examples = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $name The name of the parameter. REQUIRED.
     * @param string $in The location of the parameter. REQUIRED. Possible values: "query", "header", "path", "cookie".
     */
    public function __construct(string $name, string $in) {
        $this->setName($name);
        $this->setIn($in);
    }
    
    /**
     * Sets the name of the parameter.
     * 
     * @param string $name The name of the parameter.
     * 
     * @return ParameterObj
     */
    public function setName(string $name): ParameterObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the name of the parameter.
     * 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * Sets the location of the parameter.
     * 
     * @param string $in The location. Possible values: "query", "header", "path", "cookie".
     * 
     * @return ParameterObj
     */
    public function setIn(string $in): ParameterObj {
        $this->in = $in;
        if ($in === 'path') {
            $this->required = true;
        }
        return $this;
    }
    
    /**
     * Returns the location of the parameter.
     * 
     * @return string
     */
    public function getIn(): string {
        return $this->in;
    }
    
    /**
     * Sets the description of the parameter.
     * 
     * @param string $description A brief description of the parameter.
     * 
     * @return ParameterObj
     */
    public function setDescription(string $description): ParameterObj {
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
     * Sets whether this parameter is mandatory.
     * 
     * @param bool $required True if required.
     * 
     * @return ParameterObj
     */
    public function setRequired(bool $required): ParameterObj {
        $this->required = $required;
        return $this;
    }
    
    /**
     * Returns whether this parameter is required.
     * 
     * @return bool
     */
    public function isRequired(): bool {
        return $this->required;
    }
    
    /**
     * Sets whether this parameter is deprecated.
     * 
     * @param bool $deprecated True if deprecated.
     * 
     * @return ParameterObj
     */
    public function setDeprecated(bool $deprecated): ParameterObj {
        $this->deprecated = $deprecated;
        return $this;
    }
    
    /**
     * Returns whether this parameter is deprecated.
     * 
     * @return bool
     */
    public function isDeprecated(): bool {
        return $this->deprecated;
    }
    
    /**
     * Sets whether to allow empty value.
     * 
     * @param bool $allowEmptyValue True to allow empty value.
     * 
     * @return ParameterObj
     */
    public function setAllowEmptyValue(bool $allowEmptyValue): ParameterObj {
        $this->allowEmptyValue = $allowEmptyValue;
        return $this;
    }
    
    /**
     * Returns whether empty value is allowed.
     * 
     * @return bool
     */
    public function isAllowEmptyValue(): bool {
        return $this->allowEmptyValue;
    }
    
    /**
     * Sets the serialization style.
     * 
     * @param string $style The style value.
     * 
     * @return ParameterObj
     */
    public function setStyle(string $style): ParameterObj {
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
     * @return ParameterObj
     */
    public function setExplode(bool $explode): ParameterObj {
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
     * Sets whether to allow reserved characters.
     * 
     * @param bool $allowReserved True to allow reserved characters.
     * 
     * @return ParameterObj
     */
    public function setAllowReserved(bool $allowReserved): ParameterObj {
        $this->allowReserved = $allowReserved;
        return $this;
    }
    
    /**
     * Returns whether reserved characters are allowed.
     * 
     * @return bool|null
     */
    public function getAllowReserved(): ?bool {
        return $this->allowReserved;
    }
    
    /**
     * Sets the schema defining the type used for the parameter.
     * 
     * @param mixed $schema Schema Object or any schema definition.
     * 
     * @return ParameterObj
     */
    public function setSchema($schema): ParameterObj {
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
     * Sets an example of the parameter's potential value.
     * 
     * @param mixed $example Example value.
     * 
     * @return ParameterObj
     */
    public function setExample($example): ParameterObj {
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
     * Sets examples of the parameter's potential value.
     * 
     * @param array $examples Map of example names to Example Objects or Reference Objects.
     * 
     * @return ParameterObj
     */
    public function setExamples(array $examples): ParameterObj {
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
     * Returns a Json object that represents the Parameter Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('name', $this->name);
        $json->add('in', $this->in);
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        if ($this->required) {
            $json->add('required', $this->required);
        }
        
        if ($this->deprecated) {
            $json->add('deprecated', $this->deprecated);
        }
        
        if ($this->allowEmptyValue) {
            $json->add('allowEmptyValue', $this->allowEmptyValue);
        }
        
        if ($this->style !== null) {
            $json->add('style', $this->style);
        }
        
        if ($this->explode !== null) {
            $json->add('explode', $this->explode);
        }
        
        if ($this->allowReserved !== null) {
            $json->add('allowReserved', $this->allowReserved);
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
