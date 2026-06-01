<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;

/**
 * Base class for OpenAPI specification objects.
 * 
 * Provides common properties (description, deprecated, etc.) and a helper
 * for building JSON output without repetitive null-checks.
 *
 * @author Ibrahim
 */
abstract class OpenAPIObject {
    private ?string $description = null;

    public function getDescription() : ?string {
        return $this->description;
    }

    public function setDescription(string $description) : static {
        $this->description = $description;
        return $this;
    }
    /**
     * Adds a value to a Json object only if it is not null.
     * 
     * @param Json $json The target Json object.
     * @param string $key The JSON key.
     * @param mixed $value The value to add.
     */
    protected function addIfNotNull(Json $json, string $key, mixed $value) : void {
        if ($value !== null) {
            $json->add($key, $value);
        }
    }
    /**
     * Adds a value to a Json object only if it is truthy (non-null, non-false, non-empty).
     * 
     * @param Json $json The target Json object.
     * @param string $key The JSON key.
     * @param mixed $value The value to add.
     */
    protected function addIfTruthy(Json $json, string $key, mixed $value) : void {
        if (!empty($value)) {
            $json->add($key, $value);
        }
    }
}
