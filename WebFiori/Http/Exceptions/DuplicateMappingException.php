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
namespace WebFiori\Http\Exceptions;

/**
 * Exception thrown when duplicate HTTP method mappings are detected.
 * 
 * @author Ibrahim
 */
class DuplicateMappingException extends \Exception {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}
