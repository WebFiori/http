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
 * Exception for 401 Unauthorized responses.
 * 
 * @author Ibrahim
 */
class UnauthorizedException extends HttpException {
    
    public function __construct(string $message = 'Unauthorized') {
        parent::__construct($message, 401, 'error');
    }
}
