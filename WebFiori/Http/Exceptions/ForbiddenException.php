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
 * Exception for 403 Forbidden responses.
 * 
 * @author Ibrahim
 */
class ForbiddenException extends HttpException {
    public function __construct(string $message = 'Forbidden') {
        parent::__construct($message, 403, 'error');
    }
}
