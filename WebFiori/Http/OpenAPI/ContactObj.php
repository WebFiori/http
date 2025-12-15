<?php
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Contact Object in OpenAPI specification.
 * 
 * Contact information for the exposed API.
 */
class ContactObj implements JsonI {
    private ?string $name = null;
    private ?string $url = null;
    private ?string $email = null;
    
    /**
     * Sets the identifying name of the contact person/organization.
     * 
     * @param string $name The identifying name.
     * 
     * @return ContactObj
     */
    public function setName(string $name): ContactObj {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the contact name.
     * 
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }
    
    /**
     * Sets the URI for the contact information.
     * 
     * @param string $url The URI for the contact information. This MUST be in the form of a URI.
     * 
     * @return ContactObj
     */
    public function setUrl(string $url): ContactObj {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Returns the contact URL.
     * 
     * @return string|null
     */
    public function getUrl(): ?string {
        return $this->url;
    }
    
    /**
     * Sets the email address of the contact person/organization.
     * 
     * @param string $email The email address. This MUST be in the form of an email address.
     * 
     * @return ContactObj
     */
    public function setEmail(string $email): ContactObj {
        $this->email = $email;
        return $this;
    }
    
    /**
     * Returns the contact email.
     * 
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }
    
    /**
     * Returns a Json object that represents the Contact Object.
     * 
     * @return Json
     */
    public function toJSON(): Json {
        $json = new Json();
        
        if ($this->name !== null) {
            $json->add('name', $this->name);
        }
        
        if ($this->url !== null) {
            $json->add('url', $this->url);
        }
        
        if ($this->email !== null) {
            $json->add('email', $this->email);
        }
        
        return $json;
    }
}
