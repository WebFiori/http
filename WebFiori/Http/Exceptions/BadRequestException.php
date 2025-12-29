<?php
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
