<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 */
namespace WebFiori\Http\Annotations;

use Attribute;

/**
 * Declares the content types a method can consume (accept in request body).
 * 
 * Used for per-method content type control — overrides the default allowed
 * types (application/x-www-form-urlencoded, multipart/form-data, application/json)
 * for POST and PUT requests.
 * 
 * When a non-standard content type is consumed (one that is not form-encoded
 * or JSON), parameter filtering/parsing is skipped and the raw body is
 * available via php://input.
 * 
 * Usage:
 * ```php
 * #[PostMapping]
 * #[Consumes(MediaType::OCTET_STREAM)]
 * public function uploadFile(): ResponseEntity { ... }
 * ```
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Consumes {
    public readonly array $contentTypes;

    public function __construct(string ...$contentTypes) {
        $this->contentTypes = $contentTypes;
    }
}
