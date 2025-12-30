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
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an OAuth Flow Object in OpenAPI specification.
 * 
 * Configuration details for a supported OAuth Flow.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#oauth-flow-object
 */
class OAuthFlowObj implements JsonI {
    /**
     * The authorization URL to be used for this flow.
     * 
     * This MUST be in the form of a URL. The OAuth2 standard requires the use of TLS.
     * 
     * REQUIRED for oauth2 ("implicit", "authorizationCode") flows.
     * 
     * @var string|null
     */
    private ?string $authorizationUrl = null;
    
    /**
     * The token URL to be used for this flow.
     * 
     * This MUST be in the form of a URL. The OAuth2 standard requires the use of TLS.
     * 
     * REQUIRED for oauth2 ("password", "clientCredentials", "authorizationCode") flows.
     * 
     * @var string|null
     */
    private ?string $tokenUrl = null;
    
    /**
     * The URL to be used for obtaining refresh tokens.
     * 
     * This MUST be in the form of a URL. The OAuth2 standard requires the use of TLS.
     * 
     * @var string|null
     */
    private ?string $refreshUrl = null;
    
    /**
     * The available scopes for the OAuth2 security scheme.
     * 
     * A map between the scope name and a short description for it. The map MAY be empty.
     * 
     * REQUIRED.
     * 
     * @var array
     */
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
     * @return OAuthFlowObj Returns self for method chaining.
     */
    public function setAuthorizationUrl(string $authorizationUrl): OAuthFlowObj {
        $this->authorizationUrl = $authorizationUrl;
        return $this;
    }
    
    /**
     * Returns the authorization URL.
     * 
     * @return string|null Returns the value, or null if not set.
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
     * @return OAuthFlowObj Returns self for method chaining.
     */
    public function setTokenUrl(string $tokenUrl): OAuthFlowObj {
        $this->tokenUrl = $tokenUrl;
        return $this;
    }
    
    /**
     * Returns the token URL.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getTokenUrl(): ?string {
        return $this->tokenUrl;
    }
    
    /**
     * Sets the URL to be used for obtaining refresh tokens.
     * 
     * @param string $refreshUrl The refresh URL. This MUST be in the form of a URL.
     * 
     * @return OAuthFlowObj Returns self for method chaining.
     */
    public function setRefreshUrl(string $refreshUrl): OAuthFlowObj {
        $this->refreshUrl = $refreshUrl;
        return $this;
    }
    
    /**
     * Returns the refresh URL.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getRefreshUrl(): ?string {
        return $this->refreshUrl;
    }
    
    /**
     * Sets the available scopes for the OAuth2 security scheme.
     * 
     * @param array $scopes A map between the scope name and a short description for it.
     * 
     * @return OAuthFlowObj Returns self for method chaining.
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
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json([
            'scopes' => $this->getScopes()
        ]);
        
        if ($this->getAuthorizationUrl() !== null) {
            $json->add('authorizationUrl', $this->getAuthorizationUrl());
        }
        
        if ($this->getTokenUrl() !== null) {
            $json->add('tokenUrl', $this->getTokenUrl());
        }
        
        if ($this->getRefreshUrl() !== null) {
            $json->add('refreshUrl', $this->getRefreshUrl());
        }
        
        return $json;
    }
}
