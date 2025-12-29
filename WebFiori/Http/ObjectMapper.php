<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 * 
 */
namespace WebFiori\Http;

use Exception;
use Throwable;
use WebFiori\Json\Json;

/**
 * A class which is used to map API parameters to PHP class object.
 *
 * @author Ibrahim
 */
class ObjectMapper {
    /**
     * The name of the class to map to.
     * 
     * @var string
     */
    private $clazzName;
    private $settersMap;
    /**
     * An array that maps parameter names to setter methods.
     * 
     * @var array
     */

    /**
     * Creates new instance of the class.
     *
     * @param string $clazz The name of the class that API request will be mapped
     * to. Usually obtained using the syntax 'Class::class'.
     *
     * @param WebService $service The service at which its parameters
     * will be mapped to the object.
     *
     * @throws Exception
     */
    public function __construct(string $clazz, WebService $service) {
        $this->settersMap = [];
        $this->setClass($clazz);
        $this->extractMethodsNames($service->getInputs());
    }
    /**
     * Adds a custom method to map parameter value to a setter method.
     * 
     * Note that if setter was specified for the parameter, this method
     * will override existing one.
     * 
     * @param string $paramName The name of the parameter as it appears in request
     * body.
     * 
     * @param string|null $methodName The name of the method that the parameter will
     * be mapped to. If not provided, the name of the parameter will be used to
     * generate the name of the method as follows: Replacing every space in the
     * name by underscore. Then appending the string 'set' and capitalizing
     * first letter of the name of the parameter and capitalizing every letter 
     * after the underscore.
     * 
     */
    public function addSetterMap(string $paramName, ?string $methodName) {
        $trimmedParamName = trim($paramName);

        if (strlen($trimmedParamName) == 0) {
            return;
        }

        if ($methodName === null) {
            $methodName = 'set'.$this->paramNameToMethodName($trimmedParamName);
        }
        $this->settersMap[$methodName] = $trimmedParamName;
    }
    /**
     * Returns the name of the class that the mapper will use to map a
     * record.
     * 
     * @return string The name of the class that the mapper will use to map a
     * record.
     */
    public function getClass() : string {
        return $this->clazzName;
    }
    /**
     * Returns an array that holds the methods and each parameter they are mapped to.
     * 
     * @return array An associative array. The indices will represent methods
     * names and the values are parameters names.
     */
    public function getSettersMap() : array {
        return $this->settersMap;
    }
    /**
     * Maps a set of request parameters to the specified entity class.
     * 
     * This method will simply attempt to create an instance of the specified
     * class and use setter map to set its attributes.
     * 
     * @param array|Json $inputs An associative array that holds request inputs.
     * The indices of the array should be parameters names and the values of the
     * indices are the values fetched from request body. This can
     * be also an object of type 'Json' if request content type is 
     * 'application/json'
     * 
     * @return object|null The method will return an instance of the specified class.
     * If no class was specified, the method will return null.
     */
    public function map($inputs) {
        $clazzName = $this->getClass();

        $instance = new $clazzName();

        foreach ($this->getSettersMap() as $method => $paramName) {
            if (is_callable([$instance, $method])) {
                try {
                    if ($inputs instanceof Json) {
                        $instance->$method($inputs->get($paramName));
                    } else if (gettype($inputs) == 'array') {
                        $instance->$method($inputs[$paramName]);
                    }
                } catch (Throwable $ex) {
                }
            }
        }

        return $instance;
    }

    /**
     * Sets the class that the records will be mapped to.
     *
     * Note that the method will throw an exception if the class
     * does not exist.
     *
     * @param string $clazz The name of the class (including namespace).
     *
     * @throws Exception
     */
    public function setClass(string $clazz) {
        $trimmed = trim($clazz);

        if (class_exists($trimmed)) {
            $this->clazzName = $clazz;
        } else {
            throw new Exception('Class not found: '.$clazz);
        }
    }
    private function extractMethodsNames($inputs) {
        if ($inputs instanceof Json) {
            foreach ($inputs->getProperties() as $prop) {
                $this->addSetterMap($prop->getName(), null);
            }
        } else if (gettype($inputs) == 'array') {
            foreach (array_keys($inputs) as $name) {
                $this->addSetterMap($name, null);
            }
        }
    }
    private function paramNameToMethodName($paramName) : string {
        $expl = explode('_', str_replace('-', '_', $paramName));
        $methName = '';

        for ($x = 0 ; $x < count($expl) ; $x++) {
            $xStr = $expl[$x];
            $upper = strtoupper($xStr[0]);

            if (strlen($xStr) == 1) {
                $methName .= $upper;
            } else {
                $methName .= $upper.substr($xStr, 1);
            }
        }

        return $methName;
    }
}
