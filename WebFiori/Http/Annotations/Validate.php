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
 * Specifies a method-specific cross-field validation function.
 * 
 * The referenced method must exist on the service class, accept an array
 * of filtered inputs, and return an array of errors (empty = valid).
 * 
 * Usage:
 * ```php
 * #[Validate('validateRegistration')]
 * public function register(...): array { ... }
 * 
 * private function validateRegistration(array $inputs): array {
 *     $errors = [];
 *     if ($inputs['password'] !== $inputs['password_confirm']) {
 *         $errors['password_confirm'] = 'Passwords do not match.';
 *     }
 *     return $errors;
 * }
 * ```
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Validate {
    public function __construct(
        public readonly string $method
    ) {
    }
}
