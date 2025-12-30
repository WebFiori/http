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
 * Represents a Parameter Object in OpenAPI specification.
 * 
 * Describes a single operation parameter.
 * 
 * A unique parameter is defined by a combination of a name and location.
 * 
 * Parameter Objects MUST include either a content field or a schema field, but not both.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#parameter-object
 */
class ParameterObj implements JsonI {
    /**
     * If true, clients MAY pass a zero-length string value in place of parameters 
     * that would otherwise be omitted entirely.
     * 
     * Default value is false. This field is valid only for query parameters.
     * Use of this property is NOT RECOMMENDED.
     * 
     * @var bool
     */
    private bool $allowEmptyValue = false;

    /**
     * When this is true, parameter values are serialized using reserved expansion.
     * 
     * This field only applies to parameters with an in value of query.
     * The default value is false.
     * 
     * @var bool|null
     */
    private ?bool $allowReserved = null;

    /**
     * Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
     * 
     * Default value is false.
     * 
     * @var bool
     */
    private bool $deprecated = false;

    /**
     * A brief description of the parameter.
     * 
     * This could contain examples of use.
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @var string|null
     */
    private ?string $description = null;

    /**
     * Example of the parameter's potential value.
     * 
     * @var mixed
     */
    private $example = null;

    /**
     * Examples of the parameter's potential value.
     * 
     * Map of string to Example Object or Reference Object.
     * 
     * @var array|null
     */
    private ?array $examples = null;

    /**
     * When this is true, parameter values of type array or object generate 
     * separate parameters for each value of the array or key-value pair of the map.
     * 
     * @var bool|null
     */
    private ?bool $explode = null;

    /**
     * The location of the parameter.
     * 
     * Possible values are "query", "header", "path" or "cookie".
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $in;
    /**
     * The name of the parameter.
     * 
     * Parameter names are case sensitive.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $name;

    /**
     * Determines whether this parameter is mandatory.
     * 
     * If the parameter location is "path", this property is REQUIRED and its value MUST be true.
     * Otherwise, the property MAY be included and its default value is false.
     * 
     * @var bool
     */
    private bool $required = false;

    /**
     * The schema defining the type used for the parameter.
     * 
     * @var mixed
     */
    private $schema = null;

    /**
     * Describes how the parameter value will be serialized.
     * 
     * Default values (based on value of in): 
     * for "query" - "form"; for "path" - "simple"; 
     * for "header" - "simple"; for "cookie" - "form".
     * 
     * @var string|null
     */
    private ?string $style = null;

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

    public function getAllowEmptyValue(): bool {
        return $this->allowEmptyValue;
    }

    /**
     * Returns whether reserved characters are allowed.
     * 
     * @return bool|null Returns the value, or null if not set.
     */
    public function getAllowReserved(): ?bool {
        return $this->allowReserved;
    }

    /**
     * Returns whether this parameter is deprecated.
     * 
     * Alias for isDeprecated() for consistency with toJSON().
     * 
     * @return bool
     */
    public function getDeprecated(): bool {
        return $this->deprecated;
    }

    /**
     * Returns the description.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getDescription(): ?string {
        return $this->description;
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
     * Returns the examples.
     * 
     * @return array|null Returns the value, or null if not set.
     */
    public function getExamples(): ?array {
        return $this->examples;
    }

    /**
     * Returns the explode value.
     * 
     * @return bool|null Returns the value, or null if not set.
     */
    public function getExplode(): ?bool {
        return $this->explode;
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
     * Returns the name of the parameter.
     * 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Returns whether this parameter is required.
     * 
     * Alias for isRequired() for consistency with toJSON().
     * 
     * @return bool
     */
    public function getRequired(): bool {
        return $this->required;
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
     * Returns the style.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getStyle(): ?string {
        return $this->style;
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
     * Returns whether this parameter is deprecated.
     * 
     * @return bool
     */
    public function isDeprecated(): bool {
        return $this->deprecated;
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
     * Sets whether to allow empty value.
     * 
     * @param bool $allowEmptyValue True to allow empty value.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setAllowEmptyValue(bool $allowEmptyValue): ParameterObj {
        $this->allowEmptyValue = $allowEmptyValue;

        return $this;
    }

    /**
     * Sets whether to allow reserved characters.
     * 
     * @param bool $allowReserved True to allow reserved characters.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setAllowReserved(bool $allowReserved): ParameterObj {
        $this->allowReserved = $allowReserved;

        return $this;
    }

    /**
     * Sets whether this parameter is deprecated.
     * 
     * @param bool $deprecated True if deprecated.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setDeprecated(bool $deprecated): ParameterObj {
        $this->deprecated = $deprecated;

        return $this;
    }

    /**
     * Sets the description of the parameter.
     * 
     * @param string $description A brief description of the parameter.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setDescription(string $description): ParameterObj {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets an example of the parameter's potential value.
     * 
     * @param mixed $example Example value.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setExample($example): ParameterObj {
        $this->example = $example;

        return $this;
    }

    /**
     * Sets examples of the parameter's potential value.
     * 
     * @param array $examples Map of example names to Example Objects or Reference Objects.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setExamples(array $examples): ParameterObj {
        $this->examples = $examples;

        return $this;
    }

    /**
     * Sets the explode value.
     * 
     * @param bool $explode The explode value.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setExplode(bool $explode): ParameterObj {
        $this->explode = $explode;

        return $this;
    }

    /**
     * Sets the location of the parameter.
     * 
     * @param string $in The location. Possible values: "query", "header", "path", "cookie".
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setIn(string $in): ParameterObj {
        $this->in = $in;

        if ($in === 'path') {
            $this->required = true;
        }

        return $this;
    }

    /**
     * Sets the name of the parameter.
     * 
     * @param string $name The name of the parameter.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setName(string $name): ParameterObj {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets whether this parameter is mandatory.
     * 
     * @param bool $required True if required.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setRequired(bool $required): ParameterObj {
        $this->required = $required;

        return $this;
    }

    /**
     * Sets the schema defining the type used for the parameter.
     * 
     * @param mixed $schema Schema Object or any schema definition.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setSchema($schema): ParameterObj {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Sets the serialization style.
     * 
     * @param string $style The style value.
     * 
     * @return ParameterObj Returns self for method chaining.
     */
    public function setStyle(string $style): ParameterObj {
        $this->style = $style;

        return $this;
    }

    /**
     * Returns a Json object that represents the Parameter Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json([
            'name' => $this->getName(),
            'in' => $this->getIn()
        ]);

        if ($this->getDescription() !== null) {
            $json->add('description', $this->getDescription());
        }

        if ($this->getRequired()) {
            $json->add('required', $this->getRequired());
        }

        if ($this->getDeprecated()) {
            $json->add('deprecated', $this->getDeprecated());
        }

        if ($this->getAllowEmptyValue()) {
            $json->add('allowEmptyValue', $this->getAllowEmptyValue());
        }

        if ($this->getStyle() !== null) {
            $json->add('style', $this->getStyle());
        }

        if ($this->getExplode() !== null) {
            $json->add('explode', $this->getExplode());
        }

        if ($this->getAllowReserved() !== null) {
            $json->add('allowReserved', $this->getAllowReserved());
        }

        if ($this->getSchema() !== null) {
            $json->add('schema', $this->getSchema());
        }

        if ($this->getExample() !== null) {
            $json->add('example', $this->getExample());
        }

        if ($this->getExamples() !== null) {
            $json->add('examples', $this->getExamples());
        }

        return $json;
    }
}
