<?php
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
