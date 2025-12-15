<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an OAuth Flow Object in OpenAPI specification.
 * 
 * Configuration details for a supported OAuth Flow.
 */
class OAuthFlowObj implements JsonI {
    private ?string $authorizationUrl = null;
    private ?string $tokenUrl = null;
    private ?string $refreshUrl = null;
    private array $scopes = [];
    
    /**
     * Creates new instance.
     * 
     * @param array $scopes The available scopes for the OAuth2 security scheme.
     * A map between the scope name and a short description for it.
     */
    public function __construct(array $scopes = []) {
        $this->scopes = $scopes;
    }
    
    /**
     * Sets the authorization URL to be used for this flow.
     * 
     * @param string $authorizationUrl The authorization URL. This MUST be in the form of a URL.
     * REQUIRED for implicit and authorizationCode flows.
     * 
     * @return OAuthFlowObj
     */
    public function setAuthorizationUrl(string $authorizationUrl): OAuthFlowObj {
        $this->authorizationUrl = $authorizationUrl;
        return $this;
    }
    
    /**
     * Returns the authorization URL.
     * 
     * @return string|null
     */
    public function getAuthorizationUrl(): ?string {
        return $this->authorizationUrl;
    }
    
    /**
     * Sets the token URL to be used for this flow.
     * 
     * @param string $tokenUrl The token URL. This MUST be in the form of a URL.
     * REQUIRED for password, clientCredentials, and authorizationCode flows.
     * 
     * @return OAuthFlowObj
     */
    public function setTokenUrl(string $tokenUrl): OAuthFlowObj {
        $this->tokenUrl = $tokenUrl;
        return $this;
    }
    
    /**
     * Returns the token URL.
     * 
     * @return string|null
     */
    public function getTokenUrl(): ?string {
        return $this->tokenUrl;
    }
    
    /**
     * Sets the URL to be used for obtaining refresh tokens.
     * 
     * @param string $refreshUrl The refresh URL. This MUST be in the form of a URL.
     * 
     * @return OAuthFlowObj
     */
    public function setRefreshUrl(string $refreshUrl): OAuthFlowObj {
        $this->refreshUrl = $refreshUrl;
        return $this;
    }
    
    /**
     * Returns the refresh URL.
     * 
     * @return string|null
     */
    public function getRefreshUrl(): ?string {
        return $this->refreshUrl;
    }
    
    /**
     * Sets the available scopes for the OAuth2 security scheme.
     * 
     * @param array $scopes A map between the scope name and a short description for it.
     * 
     * @return OAuthFlowObj
     */
    public function setScopes(array $scopes): OAuthFlowObj {
        $this->scopes = $scopes;
        return $this;
    }
    
    /**
     * Adds a scope to the OAuth2 security scheme.
     * 
     * @param string $name The scope name.
     * @param string $description A short description for the scope.
     * 
     * @return OAuthFlowObj
     */
    public function addScope(string $name, string $description): OAuthFlowObj {
        $this->scopes[$name] = $description;
        return $this;
    }
    
    /**
     * Returns the available scopes.
     * 
     * @return array
     */
    public function getScopes(): array {
        return $this->scopes;
    }
    
    /**
     * Returns a Json object that represents the OAuth Flow Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->authorizationUrl !== null) {
            $json->add('authorizationUrl', $this->authorizationUrl);
        }
        
        if ($this->tokenUrl !== null) {
            $json->add('tokenUrl', $this->tokenUrl);
        }
        
        if ($this->refreshUrl !== null) {
            $json->add('refreshUrl', $this->refreshUrl);
        }
        
        $json->add('scopes', $this->scopes);
        
        return $json;
    }
}
