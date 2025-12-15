<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Server Object in OpenAPI specification.
 * 
 * An object representing a Server.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#server-object
 */
class ServerObj implements JsonI {
    /**
     * A URL to the target host.
     * 
     * This URL supports Server Variables and MAY be relative, to indicate that 
     * the host location is relative to the location where the document containing 
     * the Server Object is being served. Variable substitutions will be made when 
     * a variable is named in {braces}.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $url;
    
    /**
     * An optional string describing the host designated by the URL.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @var string|null
     */
    private ?string $description = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $url A URL to the target host. REQUIRED.
     * @param string|null $description An optional string describing the host designated by the URL.
     */
    public function __construct(string $url, ?string $description = null) {
        $this->setUrl($url);
        
        if ($description !== null) {
            $this->setDescription($description);
        }
    }
    
    /**
     * Sets the URL to the target host.
     * 
     * @param string $url A URL to the target host. This URL supports Server Variables and MAY be relative.
     * 
     * @return ServerObj Returns self for method chaining.
     */
    public function setUrl(string $url): ServerObj {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Returns the URL to the target host.
     * 
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }
    
    /**
     * Sets the description of the host designated by the URL.
     * 
     * @param string $description An optional string describing the host.
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @return ServerObj Returns self for method chaining.
     */
    public function setDescription(string $description): ServerObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description of the host.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Returns a Json object that represents the Server Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('url', $this->url);
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        return $json;
    }
}
