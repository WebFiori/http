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
 * Represents an Info Object in OpenAPI specification.
 * 
 * The object provides metadata about the API.
 * The metadata MAY be used by the clients if needed, and MAY be presented 
 * in editing or documentation generation tools for convenience.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#info-object
 */
class InfoObj implements JsonI {
    /**
     * The title of the API.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $title;
    
    /**
     * The version of the OpenAPI Document.
     * 
     * This is distinct from the OpenAPI Specification version or the version 
     * of the API being described or the version of the OpenAPI Description.
     * 
     * REQUIRED.
     * 
     * @var string
     */
    private string $version;
    
    /**
     * A short summary of the API.
     * 
     * @var string|null
     */
    private ?string $summary = null;
    
    /**
     * A description of the API.
     * 
     * CommonMark syntax MAY be used for rich text representation.
     * 
     * @var string|null
     */
    private ?string $description = null;
    
    /**
     * A URI for the Terms of Service for the API.
     * 
     * This MUST be in the form of a URI.
     * 
     * @var string|null
     */
    private ?string $termsOfService = null;
    
    /**
     * The contact information for the exposed API.
     * 
     * @var ContactObj|null
     */
    private ?ContactObj $contact = null;
    
    /**
     * The license information for the exposed API.
     * 
     * @var LicenseObj|null
     */
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
     * @return InfoObj Returns self for method chaining.
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
     * @return InfoObj Returns self for method chaining.
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
     * @return InfoObj Returns self for method chaining.
     */
    public function setSummary(string $summary): InfoObj {
        $this->summary = $summary;
        return $this;
    }
    
    /**
     * Returns the summary of the API.
     * 
     * @return string|null Returns the value, or null if not set.
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
     * @return InfoObj Returns self for method chaining.
     */
    public function setDescription(string $description): InfoObj {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Returns the description of the API.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getDescription(): ?string {
        return $this->description;
    }
    
    /**
     * Sets a URI for the Terms of Service for the API.
     * 
     * @param string $termsOfService A URI for the Terms of Service. This MUST be in the form of a URI.
     * 
     * @return InfoObj Returns self for method chaining.
     */
    public function setTermsOfService(string $termsOfService): InfoObj {
        $this->termsOfService = $termsOfService;
        return $this;
    }
    
    /**
     * Returns the Terms of Service URI.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getTermsOfService(): ?string {
        return $this->termsOfService;
    }
    
    /**
     * Sets the contact information for the exposed API.
     * 
     * @param ContactObj $contact Contact Object.
     * 
     * @return InfoObj Returns self for method chaining.
     */
    public function setContact(ContactObj $contact): InfoObj {
        $this->contact = $contact;
        return $this;
    }
    
    /**
     * Returns the contact information.
     * 
     * @return ContactObj|null Returns the value, or null if not set.
     */
    public function getContact(): ?ContactObj {
        return $this->contact;
    }
    
    /**
     * Sets the license information for the exposed API.
     * 
     * @param LicenseObj $license License Object.
     * 
     * @return InfoObj Returns self for method chaining.
     */
    public function setLicense(LicenseObj $license): InfoObj {
        $this->license = $license;
        return $this;
    }
    
    /**
     * Returns the license information.
     * 
     * @return LicenseObj|null Returns the value, or null if not set.
     */
    public function getLicense(): ?LicenseObj {
        return $this->license;
    }
    
    /**
     * Returns a Json object that represents the Info Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json([
            'title' => $this->getTitle(),
            'version' => $this->getVersion()
        ]);
        
        
        if ($this->getSummary() !== null) {
            $json->add('summary', $this->getSummary());
        }
        
        if ($this->getDescription() !== null) {
            $json->add('description', $this->getDescription());
        }
        
        if ($this->getTermsOfService() !== null) {
            $json->add('termsOfService', $this->getTermsOfService());
        }
        
        if ($this->getContact() !== null) {
            $json->add('contact', $this->getContact());
        }
        
        if ($this->getLicense() !== null) {
            $json->add('license', $this->getLicense());
        }
        
        return $json;
    }
}
