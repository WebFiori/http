<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Security Scheme Object in OpenAPI specification.
 * 
 * Defines a security scheme that can be used by the operations.
 */
class SecuritySchemeObj implements JsonI {
    private string $type;
    private ?string $description = null;
    private ?string $name = null;
    private ?string $in = null;
    private ?string $scheme = null;
    private ?string $bearerFormat = null;
    private ?OAuthFlowsObj $flows = null;
    private ?string $openIdConnectUrl = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $type The type of the security scheme. REQUIRED.
     * Valid values are "apiKey", "http", "mutualTLS", "oauth2", "openIdConnect".
     */
    public function __construct(string $type) {
        $this->setType($type);
    }
    
    /**
     * Sets the type of the security scheme.
     * 
     * @param string $type Valid values are "apiKey", "http", "mutualTLS", "oauth2", "openIdConnect".
     * 
     * @return SecuritySchemeObj
     */
    public function setType(string $type): SecuritySchemeObj {
        $this->type = $type;
        return $this;
    }
    
    /**
     * Returns the type of the security scheme.
     * 
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }
    
    /**
     * Sets the description for security scheme.
     * 
     * @param string $description A description for security scheme.
     * 
     * @return SecuritySchemeObj
     */
    public function setDescription(string $description): SecuritySchemeObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description.
     * 
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets the name of the header, query or cookie parameter to be used.
     * 
     * @param string $name The parameter name. REQUIRED for apiKey type.
     * 
     * @return SecuritySchemeObj
     */
    public function setName(string $name): SecuritySchemeObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the parameter name.
     * 
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }
    
    /**
     * Sets the location of the API key.
     * 
     * @param string $in Valid values are "query", "header", or "cookie". REQUIRED for apiKey type.
     * 
     * @return SecuritySchemeObj
     */
    public function setIn(string $in): SecuritySchemeObj {
        $this->in = $in;
        return $this;
    }
    
    /**
     * Returns the location of the API key.
     * 
     * @return string|null
     */
    public function getIn(): ?string {
        return $this->in;
    }
    
    /**
     * Sets the name of the HTTP Authentication scheme.
     * 
     * @param string $scheme The HTTP Authentication scheme. REQUIRED for http type.
     * 
     * @return SecuritySchemeObj
     */
    public function setScheme(string $scheme): SecuritySchemeObj {
        $this->scheme = $scheme;
        return $this;
    }
    
    /**
     * Returns the HTTP Authentication scheme.
     * 
     * @return string|null
     */
    public function getScheme(): ?string {
        return $this->scheme;
    }
    
    /**
     * Sets a hint to identify how the bearer token is formatted.
     * 
     * @param string $bearerFormat The bearer token format (e.g., "JWT").
     * 
     * @return SecuritySchemeObj
     */
    public function setBearerFormat(string $bearerFormat): SecuritySchemeObj {
        $this->bearerFormat = $bearerFormat;
        return $this;
    }
    
    /**
     * Returns the bearer token format.
     * 
     * @return string|null
     */
    public function getBearerFormat(): ?string {
        return $this->bearerFormat;
    }
    
    /**
     * Sets configuration information for the OAuth2 flow types supported.
     * 
     * @param OAuthFlowsObj $flows OAuth Flows Object. REQUIRED for oauth2 type.
     * 
     * @return SecuritySchemeObj
     */
    public function setFlows(OAuthFlowsObj $flows): SecuritySchemeObj {
        $this->flows = $flows;
        return $this;
    }
    
    /**
     * Returns the OAuth flows configuration.
     * 
     * @return OAuthFlowsObj|null
     */
    public function getFlows(): ?OAuthFlowsObj {
        return $this->flows;
    }
    
    /**
     * Sets the OpenID Connect discovery URL.
     * 
     * @param string $openIdConnectUrl Well-known URL to discover the provider metadata.
     * REQUIRED for openIdConnect type.
     * 
     * @return SecuritySchemeObj
     */
    public function setOpenIdConnectUrl(string $openIdConnectUrl): SecuritySchemeObj {
        $this->openIdConnectUrl = $openIdConnectUrl;
        return $this;
    }
    
    /**
     * Returns the OpenID Connect discovery URL.
     * 
     * @return string|null
     */
    public function getOpenIdConnectUrl(): ?string {
        return $this->openIdConnectUrl;
    }
    
    /**
     * Returns a Json object that represents the Security Scheme Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('type', $this->type);
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        if ($this->name !== null) {
            $json->add('name', $this->name);
        }
        
        if ($this->in !== null) {
            $json->add('in', $this->in);
        }
        
        if ($this->scheme !== null) {
            $json->add('scheme', $this->scheme);
        }
        
        if ($this->bearerFormat !== null) {
            $json->add('bearerFormat', $this->bearerFormat);
        }
        
        if ($this->flows !== null) {
            $json->add('flows', $this->flows);
        }
        
        if ($this->openIdConnectUrl !== null) {
            $json->add('openIdConnectUrl', $this->openIdConnectUrl);
        }
        
        return $json;
    }
}
