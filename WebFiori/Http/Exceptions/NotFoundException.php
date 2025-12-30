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
 * Exception for 404 Not Found responses.
 * 
 * @author Ibrahim
 */
class NotFoundException extends HttpException {
    public function __construct(string $message = 'Not Found') {
        parent::__construct($message, 404, 'error');
    }
}
