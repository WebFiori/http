<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a License Object in OpenAPI specification.
 * 
 * License information for the exposed API.
 */
class LicenseObj implements JsonI {
    private string $name;
    private ?string $identifier = null;
    private ?string $url = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $name The license name used for the API. REQUIRED.
     */
    public function __construct(string $name) {
        $this->setName($name);
    }
    
    /**
     * Sets the license name used for the API.
     * 
     * @param string $name The license name.
     * 
     * @return LicenseObj
     */
    public function setName(string $name): LicenseObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the license name.
     * 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * Sets an SPDX license expression for the API.
     * 
     * @param string $identifier An SPDX license expression.
     * The identifier field is mutually exclusive of the url field.
     * 
     * @return LicenseObj
     */
    public function setIdentifier(string $identifier): LicenseObj {
        $this->identifier = $identifier;
        $this->url = null;
        return $this;
    }
    
    /**
     * Returns the SPDX license identifier.
     * 
     * @return string|null
     */
    public function getIdentifier(): ?string {
        return $this->identifier;
    }
    
    /**
     * Sets a URI for the license used for the API.
     * 
     * @param string $url A URI for the license. This MUST be in the form of a URI.
     * The url field is mutually exclusive of the identifier field.
     * 
     * @return LicenseObj
     */
    public function setUrl(string $url): LicenseObj {
        $this->url = $url;
        $this->identifier = null;
        return $this;
    }
    
    /**
     * Returns the license URL.
     * 
     * @return string|null
     */
    public function getUrl(): ?string {
        return $this->url;
    }
    
    /**
     * Returns a Json object that represents the License Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('name', $this->name);
        
        if ($this->identifier !== null) {
            $json->add('identifier', $this->identifier);
        }
        
        if ($this->url !== null) {
            $json->add('url', $this->url);
        }
        
        return $json;
    }
}
