<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an OAuth Flows Object in OpenAPI specification.
 * 
 * Allows configuration of the supported OAuth Flows.
 */
class OAuthFlowsObj implements JsonI {
    private ?OAuthFlowObj $implicit = null;
    private ?OAuthFlowObj $password = null;
    private ?OAuthFlowObj $clientCredentials = null;
    private ?OAuthFlowObj $authorizationCode = null;
    
    /**
     * Sets configuration for the OAuth Implicit flow.
     * 
     * @param OAuthFlowObj $implicit OAuth Flow Object for implicit flow.
     * 
     * @return OAuthFlowsObj
     */
    public function setImplicit(OAuthFlowObj $implicit): OAuthFlowsObj {
        $this->implicit = $implicit;
        return $this;
    }
    
    /**
     * Returns the implicit flow configuration.
     * 
     * @return OAuthFlowObj|null
     */
    public function getImplicit(): ?OAuthFlowObj {
        return $this->implicit;
    }
    
    /**
     * Sets configuration for the OAuth Resource Owner Password flow.
     * 
     * @param OAuthFlowObj $password OAuth Flow Object for password flow.
     * 
     * @return OAuthFlowsObj
     */
    public function setPassword(OAuthFlowObj $password): OAuthFlowsObj {
        $this->password = $password;
        return $this;
    }
    
    /**
     * Returns the password flow configuration.
     * 
     * @return OAuthFlowObj|null
     */
    public function getPassword(): ?OAuthFlowObj {
        return $this->password;
    }
    
    /**
     * Sets configuration for the OAuth Client Credentials flow.
     * 
     * @param OAuthFlowObj $clientCredentials OAuth Flow Object for client credentials flow.
     * 
     * @return OAuthFlowsObj
     */
    public function setClientCredentials(OAuthFlowObj $clientCredentials): OAuthFlowsObj {
        $this->clientCredentials = $clientCredentials;
        return $this;
    }
    
    /**
     * Returns the client credentials flow configuration.
     * 
     * @return OAuthFlowObj|null
     */
    public function getClientCredentials(): ?OAuthFlowObj {
        return $this->clientCredentials;
    }
    
    /**
     * Sets configuration for the OAuth Authorization Code flow.
     * 
     * @param OAuthFlowObj $authorizationCode OAuth Flow Object for authorization code flow.
     * 
     * @return OAuthFlowsObj
     */
    public function setAuthorizationCode(OAuthFlowObj $authorizationCode): OAuthFlowsObj {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }
    
    /**
     * Returns the authorization code flow configuration.
     * 
     * @return OAuthFlowObj|null
     */
    public function getAuthorizationCode(): ?OAuthFlowObj {
        return $this->authorizationCode;
    }
    
    /**
     * Returns a Json object that represents the OAuth Flows Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->implicit !== null) {
            $json->add('implicit', $this->implicit);
        }
        
        if ($this->password !== null) {
            $json->add('password', $this->password);
        }
        
        if ($this->clientCredentials !== null) {
            $json->add('clientCredentials', $this->clientCredentials);
        }
        
        if ($this->authorizationCode !== null) {
            $json->add('authorizationCode', $this->authorizationCode);
        }
        
        return $json;
    }
}
