<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Contact Object in OpenAPI specification.
 * 
 * Contact information for the exposed API.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#contact-object
 */
class ContactObj implements JsonI {
    /**
     * The identifying name of the contact person/organization.
     * 
     * @var string|null
     */
    private ?string $name = null;
    
    /**
     * The URI for the contact information.
     * 
     * This MUST be in the form of a URI.
     * 
     * @var string|null
     */
    private ?string $url = null;
    
    /**
     * The email address of the contact person/organization.
     * 
     * This MUST be in the form of an email address.
     * 
     * @var string|null
     */
    private ?string $email = null;
    
    /**
     * Sets the identifying name of the contact person/organization.
     * 
     * @param string $name The identifying name.
     * 
     * @return ContactObj Returns self for method chaining.
     */
    public function setName(string $name): ContactObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the contact name.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getName(): ?string {
        return $this->name;
    }
    
    /**
     * Sets the URI for the contact information.
     * 
     * @param string $url The URI for the contact information. This MUST be in the form of a URI.
     * 
     * @return ContactObj Returns self for method chaining.
     */
    public function setUrl(string $url): ContactObj {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Returns the contact URL.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getUrl(): ?string {
        return $this->url;
    }
    
    /**
     * Sets the email address of the contact person/organization.
     * 
     * @param string $email The email address. This MUST be in the form of an email address.
     * 
     * @return ContactObj Returns self for method chaining.
     */
    public function setEmail(string $email): ContactObj {
        $this->email = $email;
        return $this;
    }
    
    /**
     * Returns the contact email.
     * 
     * @return string|null Returns the value, or null if not set.
     */
    public function getEmail(): ?string {
        return $this->email;
    }
    
    /**
     * Returns a Json object that represents the Contact Object.
     * 
     * @return Json A Json object representation following OpenAPI 3.1.0 specification.
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->getName() !== null) {
            $json->add('name', $this->getName());
        }
        
        if ($this->getUrl() !== null) {
            $json->add('url', $this->getUrl());
        }
        
        if ($this->getEmail() !== null) {
            $json->add('email', $this->getEmail());
        }
        
        return $json;
    }
}
