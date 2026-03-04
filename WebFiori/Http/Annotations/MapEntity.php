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
 * Attribute for mapping HTTP request parameters to entity objects
 */
#[Attribute(Attribute::TARGET_METHOD)]
class MapEntity {
    public function __construct(
        public string $entityClass,
        public array $setters = []
    ) {
    }
}
