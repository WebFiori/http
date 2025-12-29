<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * A class which holds the names of allowed options in request parameter.
 *
 * @author Ibrahim
 */
class ParamOption {
    /**
     * An option which is used to set default value if parameter is optional and
     * not provided.
     */
    const DEFAULT = 'default';
    /**
     * An option which is used to set the methods at which the parameter must exist.
     */
    const METHODS = 'methods';
    /**
     * An option which is used to set a description for the parameter
     */
    const DESCRIPTION = 'description';
    /**
     * An option which is used to allow empty strings as values. Applicable to string only.
     */
    const EMPTY = 'allow-empty';
    /**
     * An option which is used to set custom filtering function on the parameter.
     */
    const FILTER = 'custom-filter';
    /**
     * An option which is used to set maximum allowed value. Applicable to numerical
     * types only.
     */
    const MAX = 'max';
    /**
     * An option which is used to set minimum allowed length. Applicable to string types only.
     */
    const MAX_LENGTH = 'max-length';
    /**
     * An option which is used to set minimum allowed value. Applicable to numerical
     * types only.
     */
    const MIN = 'min';
    /**
     * An option which is used to set minimum allowed length. Applicable to string types only.
     */
    const MIN_LENGTH = 'min-length';
    /**
     * Parameter name option. Applies to all data types.
     */
    const NAME = 'name';
    /**
     * An option which is used to indicate that a parameter is optional or not (bool). Applies to all data types.
     */
    const OPTIONAL = 'optional';
    /**
     * Parameter type option. Applies to all data types.
     */
    const TYPE = 'type';
}
