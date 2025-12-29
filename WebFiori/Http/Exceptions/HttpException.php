<?php
namespace WebFiori\Http\Exceptions;

use Exception;

/**
 * Base class for HTTP exceptions that can be automatically converted to HTTP responses.
 * 
 * @author Ibrahim
 */
abstract class HttpException extends Exception {
    
    protected int $statusCode;
    protected string $responseType;
    
    public function __construct(string $message = '', int $statusCode = 500, string $responseType = 'error') {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->responseType = $responseType;
    }
    
    public function getStatusCode(): int {
        return $this->statusCode;
    }
    
    public function getResponseType(): string {
        return $this->responseType;
    }
}
