<?php
namespace WebFiori\Http\Exceptions;

/**
 * Exception thrown when duplicate HTTP method mappings are detected.
 * 
 * @author Ibrahim
 */
class DuplicateMappingException extends \Exception {
    
    public function __construct(string $message) {
        parent::__construct($message);
    }
}
