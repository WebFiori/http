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

/**
 * Represents an OpenAPI content type (media type) definition.
 *
 * @author Ibrahim
 */
class ContentType {
    private string $mediaType;
    private Schema $schema;

    /**
     * Creates a new content type.
     * 
     * @param string $mediaType Media type (e.g., 'application/json', 'application/xml')
     * @param Schema $schema The schema for this content type
     */
    public function __construct(string $mediaType, Schema $schema) {
        $this->mediaType = $mediaType;
        $this->schema = $schema;
    }

    /**
     * Gets the media type.
     * 
     * @return string
     */
    public function getMediaType(): string {
        return $this->mediaType;
    }

    /**
     * Gets the schema.
     * 
     * @return Schema
     */
    public function getSchema(): Schema {
        return $this->schema;
    }

    /**
     * Converts the content type to JSON representation.
     * 
     * @return Json JSON object
     */
    public function toJson(): Json {
        return new Json(['schema' => $this->schema->toJson()]);
    }
}
