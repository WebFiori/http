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

use WebFiori\Http\ParamType;
use WebFiori\Http\RequestParameter;
use WebFiori\Json\Json;

/**
 * Represents an OpenAPI 3.1 Schema object.
 *
 * @author Ibrahim
 */
class Schema {
    private mixed $default = null;
    private ?string $description = null;
    private ?array $enum = null;
    private array $examples = [];
    private ?string $format = null;
    private mixed $maximum = null;
    private ?int $maxLength = null;
    private mixed $minimum = null;
    private ?int $minLength = null;
    private ?string $pattern = null;
    private ?string $type = null;

    /**
     * Creates a new schema.
     * 
     * @param string|null $type The schema type
     */
    public function __construct(?string $type = null) {
        $this->type = $type;
    }

    /**
     * Adds an example value.
     * 
     * @param mixed $example Example value
     * 
     * @return Schema
     */
    public function addExample(mixed $example): self {
        $this->examples[] = $example;

        return $this;
    }

    /**
     * Creates a Schema from a RequestParameter.
     * 
     * @param RequestParameter $param The request parameter
     * 
     * @return Schema The schema object
     */
    public static function fromRequestParameter(RequestParameter $param): self {
        $schema = new self();
        $schema->type = self::mapType($param->getType());

        // Set format for special types
        if ($param->getType() === ParamType::EMAIL) {
            $schema->format = 'email';
        } else if ($param->getType() === ParamType::URL) {
            $schema->format = 'uri';
        }

        // Constraints
        $schema->minimum = $param->getMinValue();
        $schema->maximum = $param->getMaxValue();
        $schema->minLength = $param->getMinLength();
        $schema->maxLength = $param->getMaxLength();
        $schema->default = $param->getDefault();
        $schema->description = $param->getDescription();

        return $schema;
    }

    /**
     * Maps internal parameter types to OpenAPI types.
     * 
     * @param string $type Internal type
     * 
     * @return string OpenAPI type
     */
    public static function mapType(string $type): string {
        $typeMap = [
            ParamType::INT => 'integer',
            ParamType::DOUBLE => 'number',
            ParamType::STRING => 'string',
            ParamType::BOOL => 'boolean',
            ParamType::ARR => 'array',
            ParamType::EMAIL => 'string',
            ParamType::URL => 'string',
            ParamType::JSON_OBJ => 'object'
        ];

        return $typeMap[strtolower($type)] ?? 'string';
    }

    /**
     * Sets allowed enum values.
     * 
     * @param array $values Array of allowed values
     * 
     * @return Schema
     */
    public function setEnum(array $values): self {
        $this->enum = $values;

        return $this;
    }

    /**
     * Sets the format.
     * 
     * @param string $format Format (e.g., 'email', 'uri', 'date-time')
     * 
     * @return Schema
     */
    public function setFormat(string $format): self {
        $this->format = $format;

        return $this;
    }

    /**
     * Sets the pattern (regex).
     * 
     * @param string $pattern Regular expression pattern
     * 
     * @return Schema
     */
    public function setPattern(string $pattern): self {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Converts the schema to JSON representation.
     * 
     * @return Json JSON object
     */
    public function toJson(): Json {
        $json = new Json();

        if ($this->type !== null) {
            $json->add('type', $this->type);
        }

        if ($this->format !== null) {
            $json->add('format', $this->format);
        }

        if ($this->default !== null) {
            $json->add('default', $this->default);
        }

        if ($this->minimum !== null) {
            $json->add('minimum', $this->minimum);
        }

        if ($this->maximum !== null) {
            $json->add('maximum', $this->maximum);
        }

        if ($this->minLength !== null) {
            $json->add('minLength', $this->minLength);
        }

        if ($this->maxLength !== null) {
            $json->add('maxLength', $this->maxLength);
        }

        if ($this->pattern !== null) {
            $json->add('pattern', $this->pattern);
        }

        if ($this->enum !== null) {
            $json->add('enum', $this->enum);
        }

        if (!empty($this->examples)) {
            $json->add('examples', $this->examples);
        }

        return $json;
    }
}
