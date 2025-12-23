<?php
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
