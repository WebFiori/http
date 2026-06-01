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
 * Represents a Header Object in OpenAPI specification.
 * 
 * The Header Object follows the structure of the Parameter Object with the following changes:
 * - name MUST NOT be specified, it is given in the corresponding headers map.
 * - in MUST NOT be specified, it is implicitly in header.
 * - All traits that are affected by the location MUST be applicable to a location of header 
 *   (for example, style).
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#header-object
 */
class HeaderObj extends OpenAPIObject implements JsonI {
    /**
     * Specifies that the header is deprecated and SHOULD be transitioned out of usage.
     * 
     * Default value is false.
     * 
     * @var bool
     */
    private bool $deprecated = false;

    /**
     * Example of the header's potential value.
     * 
     * @var mixed
     */
    private $example = null;

    /**
     * Examples of the header's potential value.
     * 
     * Map of string to Example Object or Reference Object.
     * 
     * @var array|null
     */
    private ?array $examples = null;

    /**
     * When this is true, header values of type array or object generate a single header 
     * whose value is a comma-separated list of the array items or key-value pairs of the map.
     * 
     * For other data types this field has no effect. The default value is false.
     * 
     * @var bool|null
     */
    private ?bool $explode = null;

    /**
     * Determines whether this header is mandatory.
     * 
     * The default value is false.
     * 
     * @var bool
     */
    private bool $required = false;

    /**
     * The schema defining the type used for the header.
     * 
     * @var mixed
     */
    private $schema = null;

    /**
     * Describes how the header value will be serialized.
     * 
     * The default (and only legal value for headers) is "simple".
     * 
     * @var string|null
     */
    private ?string $style = null;

    public function getDeprecated(): bool {
        return $this->deprecated;
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
     * Returns whether this header is deprecated.
     * 
     * @return bool
     */
    public function isDeprecated(): bool {
        return $this->deprecated;
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
     * @return HeaderObj Returns self for method chaining.
     */
    public function setDeprecated(bool $deprecated): HeaderObj {
        $this->deprecated = $deprecated;

        return $this;
    }

    /**
     * Sets an example of the header's potential value.
     * 
     * @param mixed $example Example value.
     * 
     * @return HeaderObj Returns self for method chaining.
     */
    public function setExample($example): HeaderObj {
        $this->example = $example;

        return $this;
    }

    /**
     * Sets examples of the header's potential value.
     * 
     * @param array $examples Map of example names to Example Objects or Reference Objects.
     * 
     * @return HeaderObj Returns self for method chaining.
     */
    public function setExamples(array $examples): HeaderObj {
        $this->examples = $examples;

        return $this;
    }

    /**
     * Sets the explode value.
     * 
     * @param bool $explode The explode value.
     * 
     * @return HeaderObj Returns self for method chaining.
     */
    public function setExplode(bool $explode): HeaderObj {
        $this->explode = $explode;

        return $this;
    }

    /**
     * Sets whether this header is mandatory.
     * 
     * @param bool $required True if required.
     * 
     * @return HeaderObj Returns self for method chaining.
     */
    public function setRequired(bool $required): HeaderObj {
        $this->required = $required;

        return $this;
    }

    /**
     * Sets the schema defining the type used for the header.
     * 
     * @param mixed $schema Schema Object or any schema definition.
     * 
     * @return HeaderObj Returns self for method chaining.
     */
    public function setSchema($schema): HeaderObj {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Sets the serialization style.
     * 
     * @param string $style The style value. Default is "simple".
     * 
     * @return HeaderObj Returns self for method chaining.
     */
    public function setStyle(string $style): HeaderObj {
        $this->style = $style;

        return $this;
    }

    /**
     * Returns a Json object that represents the Header Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();

        $this->addIfNotNull($json, 'description', $this->getDescription());
        $this->addIfTruthy($json, 'required', $this->getRequired());
        $this->addIfTruthy($json, 'deprecated', $this->getDeprecated());
        $this->addIfNotNull($json, 'style', $this->getStyle());
        $this->addIfNotNull($json, 'explode', $this->getExplode());
        $this->addIfNotNull($json, 'schema', $this->getSchema());
        $this->addIfNotNull($json, 'example', $this->getExample());
        $this->addIfNotNull($json, 'examples', $this->getExamples());

        return $json;
    }
}
