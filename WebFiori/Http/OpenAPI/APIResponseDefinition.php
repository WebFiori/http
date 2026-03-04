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
 * Represents an OpenAPI response definition.
 *
 * @author Ibrahim
 */
class APIResponseDefinition {
    private array $content = [];
    private string $description;
    private string $statusCode;

    /**
     * Creates a new response definition.
     * 
     * @param string $statusCode HTTP status code (e.g., '200', '404')
     * @param string $description Response description
     */
    public function __construct(string $statusCode, string $description) {
        $this->statusCode = $statusCode;
        $this->description = $description;
    }

    /**
     * Adds content for a specific media type.
     * 
     * @param string $mediaType Media type (e.g., 'application/json')
     * @param Schema $schema The schema for this content type
     * 
     * @return ContentType The created content type object
     */
    public function addContent(string $mediaType, Schema $schema): ContentType {
        $content = new ContentType($mediaType, $schema);
        $this->content[$mediaType] = $content;

        return $content;
    }

    /**
     * Gets the status code.
     * 
     * @return string
     */
    public function getStatusCode(): string {
        return $this->statusCode;
    }

    /**
     * Converts the response to JSON representation.
     * 
     * @return Json JSON object
     */
    public function toJson(): Json {
        $json = new Json(['description' => $this->description]);

        if (!empty($this->content)) {
            $contentJson = new Json();

            foreach ($this->content as $mediaType => $contentType) {
                $contentJson->add($mediaType, $contentType->toJson());
            }
            $json->add('content', $contentJson);
        }

        return $json;
    }
}
