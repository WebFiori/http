<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use InvalidArgumentException;

/**
 * An entity which represents a variable in URI definition.
 *
 * @author Ibrahim
 */
class UriParameter {
    private $allowedValues;
    /**
     * A boolean value that indicates if the parameter is optional or not.
     * 
     * @var bool
     */
    private $isOptional;
    private $name;
    /**
     * The name of the parameter.
     * 
     * @var string
     */
    private $value;
    /**
     * Creates new instance of the class.
     * 
     * @param string $varName The name of the variable (e.g. 'user-id').
     * In order to make the variable optional, the name can have '?' at
     * the end (e.g. 'user-id?')
     * 
     * @throws InvalidArgumentException
     */
    public function __construct(string $varName) {
        $trimmed = trim($varName);

        if (strlen($trimmed) == 0) {
            throw new InvalidArgumentException('Empty string not allowed as variable name.');
        }
        $lastChar = $varName[strlen($varName) - 1];

        if ($lastChar == '?') {
            if (strlen($trimmed) == 1) {
                throw new InvalidArgumentException('Empty string not allowed as variable name.');
            }
            $this->isOptional = true;
        } else {
            $this->isOptional = false;
        }
        $this->name = trim($trimmed, '?');
        $this->allowedValues = [];
    }
    public function addAllowedValue(string $val) : UriParameter {
        $this->allowedValues[] = trim($val);
        $currentVal = $this->getValue();

        if ($currentVal !== null && !in_array($currentVal, $this->allowedValues)) {
            $this->value = null;
        }

        return $this;
    }
    public function addAllowedValues(array $vals)  : UriParameter {
        foreach ($vals as $val) {
            $this->addAllowedValue($val);
        }

        return $this;
    }
    public function getAllowedValues() : array {
        return $this->allowedValues;
    }
    /**
     * Returns the name of the parameter.
     * 
     * @return string The name of the parameter.
     */
    public function getName() : string {
        return $this->name;
    }
    /**
     * Returns the value of the parameter.
     * 
     * @return string|null If the value of the parameter is set, it will
     * be returned as string. If not set, null is returned.
     */
    public function getValue() : ?string {
        return $this->value;
    }
    /**
     * Checks if the parameter is optional.
     * 
     * @return bool If optional, true is returned. False otherwise.
     */
    public function isOptional() : bool {
        return $this->isOptional;
    }
    /**
     * Sets the value of the parameter.
     * 
     * Note that if the parameter has a set of allowed values, the method
     * will only accept the value if its part if that set.
     * 
     * @param string $val The value of the parameter as string.
     */
    public function setValue(string $val) : bool {
        $allowed = $this->getAllowedValues();
        $trimmed = trim($val);

        if (count($allowed) > 0 && !in_array($trimmed, $allowed)) {
            return false;
        }

        if ($trimmed != '') {
            $this->value = $trimmed;

            return true;
        }

        return false;
    }
}
