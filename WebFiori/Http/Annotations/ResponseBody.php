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
namespace WebFiori\Http\Annotations;

use Attribute;

/**
 * Annotation to automatically convert method return values to HTTP responses.
 * 
 * When applied to a method, the framework will automatically process the return value
 * and convert it to an appropriate HTTP response with JSON content.
 * 
 * @author Ibrahim
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ResponseBody {
    /**
     * Creates a new ResponseBody annotation.
     * 
     * @param int $status The HTTP status code to return (default: 200)
     * @param string $type The response type indicator (default: 'success')
     * @param string $contentType The content type for the response (default: 'application/json')
     */
    public function __construct(
        public readonly int $status = 200,
        public readonly string $type = 'success',
        public readonly string $contentType = 'application/json'
    ) {}
}
