<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2026-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * Interface for grouping related request parameters into reusable sets.
 * 
 * Implement this interface to define a collection of parameters that can
 * be shared across multiple web services.
 *
 * @author Ibrahim
 */
interface ParameterSet {
    /**
     * Returns an array of parameter definitions.
     * 
     * Each key is the parameter name and the value is an associative array
     * of options compatible with WebService::addParameters().
     * 
     * @return array<string, array> Parameter definitions keyed by name.
     */
    public function getParameters(): array;
}
