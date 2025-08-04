<?php


namespace WebFiori\Http;

use InvalidArgumentException;

/**
 * A helper class which is used to parse authorization HTTP header.
 *
 */
class AuthHeader extends HttpHeader {
    private $scheme;
    private $credentials;
    /**
     * Creates new instance of the class.
     * 
     * @param string $value The value of authorization HTTP header. The value
     * must follow the following syntax: "&lt;auth-scheme&gt; &lt;authorization-parameters&gt;".
     * 
     * @throws InvalidArgumentException
     */
    public function __construct(string $value = '') {
        parent::__construct('authorization', $value);
        $split = explode(' ', $value);

        if (count($split) == 2) {
            $this->scheme = strtolower($split[0]);
            $this->credentials = $split[1];
        } else {
            throw new InvalidArgumentException("Invalid authorization header structure.");
        }
    }
    /**
     * Returns the scheme type which is used in authorization.
     * 
     * Note thar returned value will always be in lower case.
     * 
     * @return string Possible return values include "bearer", "basic" or "apikey".
     */
    public function getScheme() : string {
        return $this->scheme;
    }
    /**
     * Returns credentials part of the authorization header.
     * 
     * @return string The returned string structure will depend on scheme type.
     */
    public function getCredentials() : string {
        return $this->credentials;
    }
}
