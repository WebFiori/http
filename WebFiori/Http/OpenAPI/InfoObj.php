<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents an Info Object in OpenAPI specification.
 * 
 * The object provides metadata about the API.
 */
class InfoObj implements JsonI {
    private string $title;
    private string $version;
    private ?string $summary = null;
    private ?string $description = null;
    private ?string $termsOfService = null;
    private ?ContactObj $contact = null;
    private ?LicenseObj $license = null;
    
    /**
     * Creates new instance.
     * 
     * @param string $title The title of the API. REQUIRED.
     * @param string $version The version of the OpenAPI Document. REQUIRED.
     */
    public function __construct(string $title, string $version) {
        $this->setTitle($title);
        $this->setVersion($version);
    }
    
    /**
     * Sets the title of the API.
     * 
     * @param string $title The title of the API.
     * 
     * @return InfoObj
     */
    public function setTitle(string $title): InfoObj {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Returns the title of the API.
     * 
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }
    
    /**
     * Sets the version of the OpenAPI Document.
     * 
     * @param string $version The version of the OpenAPI Document.
     * 
     * @return InfoObj
     */
    public function setVersion(string $version): InfoObj {
        $this->version = $version;
        return $this;
    }
    
    /**
     * Returns the version of the OpenAPI Document.
     * 
     * @return string
     */
    public function getVersion(): string {
        return $this->version;
    }
    
    /**
     * Sets a short summary of the API.
     * 
     * @param string $summary A short summary of the API.
     * 
     * @return InfoObj
     */
    public function setSummary(string $summary): InfoObj {
        $this->summary = $summary;
        return $this;
    }
    
    /**
     * Returns the summary of the API.
     * 
     * @return string|null
     */
    public function getSummary(): ?string {
        return $this->summary;
    }
    
    /**
     * Sets a description of the API.
     * 
     * @param string $description A description of the API.
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @return InfoObj
     */
    public function setDescription(string $description): InfoObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description of the API.
     * 
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets a URI for the Terms of Service for the API.
     * 
     * @param string $termsOfService A URI for the Terms of Service. This MUST be in the form of a URI.
     * 
     * @return InfoObj
     */
    public function setTermsOfService(string $termsOfService): InfoObj {
        $this->termsOfService = $termsOfService;
        return $this;
    }
    
    /**
     * Returns the Terms of Service URI.
     * 
     * @return string|null
     */
    public function getTermsOfService(): ?string {
        return $this->termsOfService;
    }
    
    /**
     * Sets the contact information for the exposed API.
     * 
     * @param ContactObj $contact Contact Object.
     * 
     * @return InfoObj
     */
    public function setContact(ContactObj $contact): InfoObj {
        $this->contact = $contact;
        return $this;
    }
    
    /**
     * Returns the contact information.
     * 
     * @return ContactObj|null
     */
    public function getContact(): ?ContactObj {
        return $this->contact;
    }
    
    /**
     * Sets the license information for the exposed API.
     * 
     * @param LicenseObj $license License Object.
     * 
     * @return InfoObj
     */
    public function setLicense(LicenseObj $license): InfoObj {
        $this->license = $license;
        return $this;
    }
    
    /**
     * Returns the license information.
     * 
     * @return LicenseObj|null
     */
    public function getLicense(): ?LicenseObj {
        return $this->license;
    }
    
    /**
     * Returns a Json object that represents the Info Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        $json->add('title', $this->title);
        
        if ($this->summary !== null) {
            $json->add('summary', $this->summary);
        }
        
        if ($this->description !== null) {
            $json->add('description', $this->description);
        }
        
        if ($this->termsOfService !== null) {
            $json->add('termsOfService', $this->termsOfService);
        }
        
        if ($this->contact !== null) {
            $json->add('contact', $this->contact);
        }
        
        if ($this->license !== null) {
            $json->add('license', $this->license);
        }
        
        $json->add('version', $this->version);
        
        return $json;
    }
}
