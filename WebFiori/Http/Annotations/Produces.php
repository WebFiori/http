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
 * Declares the content types a method can produce.
 * 
 * Used for content negotiation — the framework matches the client's Accept
 * header against the declared types. If no match, returns 406 Not Acceptable.
 * 
 * Usage:
 * ```php
 * #[Produces(MediaType::JSON, MediaType::XML)]
 * public function getUser(): ResponseEntity { ... }
 * ```
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Produces {
    public readonly array $contentTypes;

    public function __construct(string ...$contentTypes) {
        $this->contentTypes = $contentTypes;
    }
}
