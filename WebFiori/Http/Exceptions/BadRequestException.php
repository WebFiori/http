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
 * Exception for 400 Bad Request responses.
 * 
 * @author Ibrahim
 */
class BadRequestException extends HttpException {
    public function __construct(string $message = 'Bad Request') {
        parent::__construct($message, 400, 'error');
    }
}
