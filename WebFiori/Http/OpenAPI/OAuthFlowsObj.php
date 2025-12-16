<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an OAuth Flows Object in OpenAPI specification.
 * 
 * Allows configuration of the supported OAuth Flows.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#oauth-flows-object
 */
class OAuthFlowsObj implements JsonI {
    /**
     * Configuration for the OAuth Implicit flow.
     * 
     * @var OAuthFlowObj|null
     */
    private ?OAuthFlowObj $implicit = null;
    
    /**
     * Configuration for the OAuth Resource Owner Password flow.
     * 
     * @var OAuthFlowObj|null
     */
    private ?OAuthFlowObj $password = null;
    
    /**
     * Configuration for the OAuth Client Credentials flow.
     * 
     * Previously called application in OpenAPI 2.0.
     * 
     * @var OAuthFlowObj|null
     */
    private ?OAuthFlowObj $clientCredentials = null;
    
    /**
     * Configuration for the OAuth Authorization Code flow.
     * 
     * Previously called accessCode in OpenAPI 2.0.
     * 
     * @var OAuthFlowObj|null
     */
    private ?OAuthFlowObj $authorizationCode = null;
    
    /**
     * Sets configuration for the OAuth Implicit flow.
     * 
     * @param OAuthFlowObj $implicit OAuth Flow Object for implicit flow.
     * 
     * @return OAuthFlowsObj Returns self for method chaining.
     */
    public function setImplicit(OAuthFlowObj $implicit): OAuthFlowsObj {
        $this->implicit = $implicit;
        return $this;
    }
    
    /**
     * Returns the implicit flow configuration.
     * 
     * @return OAuthFlowObj|null Returns the value, or null if not set.
     */
    public function getImplicit(): ?OAuthFlowObj {
        return $this->implicit;
    }
    
    /**
     * Sets configuration for the OAuth Resource Owner Password flow.
     * 
     * @param OAuthFlowObj $password OAuth Flow Object for password flow.
     * 
     * @return OAuthFlowsObj Returns self for method chaining.
     */
    public function setPassword(OAuthFlowObj $password): OAuthFlowsObj {
        $this->password = $password;
        return $this;
    }
    
    /**
     * Returns the password flow configuration.
     * 
     * @return OAuthFlowObj|null Returns the value, or null if not set.
     */
    public function getPassword(): ?OAuthFlowObj {
        return $this->password;
    }
    
    /**
     * Sets configuration for the OAuth Client Credentials flow.
     * 
     * @param OAuthFlowObj $clientCredentials OAuth Flow Object for client credentials flow.
     * 
     * @return OAuthFlowsObj Returns self for method chaining.
     */
    public function setClientCredentials(OAuthFlowObj $clientCredentials): OAuthFlowsObj {
        $this->clientCredentials = $clientCredentials;
        return $this;
    }
    
    /**
     * Returns the client credentials flow configuration.
     * 
     * @return OAuthFlowObj|null Returns the value, or null if not set.
     */
    public function getClientCredentials(): ?OAuthFlowObj {
        return $this->clientCredentials;
    }
    
    /**
     * Sets configuration for the OAuth Authorization Code flow.
     * 
     * @param OAuthFlowObj $authorizationCode OAuth Flow Object for authorization code flow.
     * 
     * @return OAuthFlowsObj Returns self for method chaining.
     */
    public function setAuthorizationCode(OAuthFlowObj $authorizationCode): OAuthFlowsObj {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }
    
    /**
     * Returns the authorization code flow configuration.
     * 
     * @return OAuthFlowObj|null Returns the value, or null if not set.
     */
    public function getAuthorizationCode(): ?OAuthFlowObj {
        return $this->authorizationCode;
    }
    
    /**
     * Returns a Json object that represents the OAuth Flows Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->getImplicit() !== null) {
            $json->add('implicit', $this->getImplicit());
        }
        
        if ($this->getPassword() !== null) {
            $json->add('password', $this->getPassword());
        }
        
        if ($this->getClientCredentials() !== null) {
            $json->add('clientCredentials', $this->getClientCredentials());
        }
        
        if ($this->getAuthorizationCode() !== null) {
            $json->add('authorizationCode', $this->getAuthorizationCode());
        }
        
        return $json;
    }
}
