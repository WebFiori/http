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
 * Declares a possible response for a service method in the OpenAPI spec.
 *
 * This attribute is repeatable so multiple responses can be declared per method.
 *
 * Usage:
 * ```php
 * #[ApiResponse(status: '200', description: 'List of products')]
 * #[ApiResponse(status: '404', description: 'Product not found')]
 * public function getProducts(): array { ... }
 * ```
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ApiResponse {
    public function __construct(
        public readonly string $status = '200',
        public readonly string $description = '',
    ) {
    }
}
