<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2024 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace webfiori\http;

/**
 * A class which holds the names of allowed options in request parameter.
 *
 * @author Ibrahim
 */
class ParamOption {
    /**
     * Parameter name option. Applies to all data types.
     */
    const NAME = 'name';
    /**
     * Parameter type option. Applies to all data types.
     */
    const TYPE = 'type';
    /**
     * An option which is used to indicate that a parameter is optional or not (bool). Applies to all data types.
     */
    const OPTIONAL = 'optional';
    /**
     * An option which is used to set default value if parameter is optional and
     * not provided.
     */
    const DEFAULT = 'default';
    /**
     * An option which is used to set minimum allowed value. Applicable to numerical
     * types only.
     */
    const MIN = 'min';
    /**
     * An option which is used to set maximum allowed value. Applicable to numerical
     * types only.
     */
    const MAX = 'maxt';
    /**
     * An option which is used to set minimum allowed length. Applicable to string types only.
     */
    const MIN_LENGTH = 'min-length';
    /**
     * An option which is used to set minimum allowed length. Applicable to string types only.
     */
    const MAX_LENGTH = 'max-length';
    /**
     * An option which is used to set custom filtering function on the parameter.
     */
    const FILTER = 'custom-filter';
    /**
     * An option which is used to set a description for the parameter
     */
    const DESCRIPTION = 'description';
    /**
     * An option which is used to allow empty strings as values. Applicable to string only.
     */
    const EMPTY = 'allow-empty';
}
