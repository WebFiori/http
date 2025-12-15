<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Server Object in OpenAPI specification.
 * 
 * An object representing a Server.
 */
class ServerObj implements JsonI {
    private string $url;
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
     * @return ServerObj
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
     * @return ServerObj
     */
    public function setDescription(string $description): ServerObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description of the host.
     * 
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Returns a Json object that represents the Server Object.
     * 
     * @return Json
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
