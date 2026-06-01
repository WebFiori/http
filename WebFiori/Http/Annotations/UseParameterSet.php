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

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class UseParameterSet {
    public function __construct(
        public readonly string $class
    ) {
    }
}
