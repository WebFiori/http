<?php
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
