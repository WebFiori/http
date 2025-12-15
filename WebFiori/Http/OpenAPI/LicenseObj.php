<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a License Object in OpenAPI specification.
 * 
 * License information for the exposed API.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#license-object
 */
class LicenseObj implements JsonI {
    /**
     * The license name used for the API.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $name;
    
    /**
     * An SPDX license expression for the API.
     * 
     * The identifier field is mutually exclusive of the url field.
     * 
     * @var string|null
     * @see https://spdx.org/licenses/
     */
    private ?string $identifier = null;
    
    /**
     * A URI for the license used for the API.
     * 
     * This MUST be in the form of a URI.
     * The url field is mutually exclusive of the identifier field.
     * 
     * @var string|null
     */
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
     * @return LicenseObj Returns self for method chaining.
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
     * @return LicenseObj Returns self for method chaining.
     */
    public function setIdentifier(string $identifier): LicenseObj {
        $this->identifier = $identifier;
        $this->url = null;
        return $this;
    }
    
    /**
     * Returns the SPDX license identifier.
     * 
     * @return string|null Returns the value, or null if not set.
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
     * @return LicenseObj Returns self for method chaining.
     */
    public function setUrl(string $url): LicenseObj {
        $this->url = $url;
        $this->identifier = null;
        return $this;
    }
    
    /**
     * Returns the license URL.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getUrl(): ?string {
        return $this->url;
    }
    
    /**
     * Returns a Json object that represents the License Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
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
