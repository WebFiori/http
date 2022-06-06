<?php
namespace webfiori\http;

/**
 * A class which is used to represents Http cookies.
 *
 * @author Ibrahim
 */
class HttpCookie {
    /**
     * Allowed values for the attribute 'Same Site'.
     */
    const SAME_SITE = [
        'Lax',
        'Strict',
        'None'
    ];
    private $expires;
    private $httpOnly;
    private $cookieName;
    private $sameSite;
    private $path;
    private $domain;
    private $secure;
    private $val;
    /**
     * Creates new instance of the class with default properties.
     */
    public function __construct() {
        $this->httpOnly = true;
        $this->cookieName = 'new-cookie';
        $this->path = '/';
        $this->secure = true;
        $this->domain = Request::getUri()->getHost();
        $this->sameSite = 'Lax';
        $this->val = hash('sha256', date('Y-m-d H:i:s'));
        $this->expires = 0;
    }
    /**
     * Sets the value of the attribute 'same site'.
     * 
     * The SameSite attribute lets servers specify whether/when cookies are sent
     * with cross-site requests. This provides some protection against 
     * cross-site request forgery attacks (CSRF).
     * 
     * @param string $val The attribute can have only one of 3 values, 'Lax',
     * 'None' and 'Strict'.
     * 
     * @return bool If set, the method will return true.
     * False otherwise.
     */
    public function setSameSite(string $val) : bool {
        $trimmed = trim($val);
        
        if (in_array($trimmed, self::SAME_SITE)) {
            $this->sameSite = $trimmed;
            return true;
        }
        return false;
    }
    /**
     * Sets cookie duration.
     * 
     * @param float $expireAfter Cookie duration in minutes.
     * 
     * @return boolean If cookie duration is updated, the method will return true. 
     * False otherwise.
     */
    public function setExpires(float $expireAfter) : bool {
        if ($expireAfter >= 0) {
            $this->expires = time() + $expireAfter;
            return true;
        }
        return false;
    }
    /**
     * Sets the path at which the cookie will operate at.
     * 
     * he Path attribute indicates a URL path that must exist in the requested 
     * URL in order to send the Cookie header.
     * 
     * @param string $path
     */
    public function setPath(string $path) {
        $this->path = trim($path);
    }
    /**
     * Sets the value of the cookie.
     * 
     * @param string $value A non-empty string that represents the value of the 
     * cookie.
     */
    public function setValue(string $value) {
        $trimmed = trim($value);
        if (strlen($trimmed) > 0) {
            $this->val = $trimmed;
        }
    }
    /**
     * Sets the name of the cookie.
     * 
     * @param string $name A non-empty string that represents the name of the 
     * cookie.
     */
    public function setName(string $name) {
        $trimmed = trim($name);
        if (strlen($trimmed) > 0) {
            $this->cookieName = $name;
        }
    }
    /**
     * Returns a string that represents the name of the cookie.
     * 
     * @return string A string that represents the name of the cookie. Default
     * return value is 'new-cookie'.
     */
    public function getName() : string {
        return $this->cookieName;
    }
    /**
     * Returns a string that represents the name of the cookie.
     * 
     * @return string A string that represents the name of the cookie. Default
     * return value is a random hash.
     */
    public function getValue() : string {
        return $this->val;
    }
    /**
     * Returns a string that represents the time at which the cookie will
     * expire at.
     * 
     * @return string If the expires is a value which is not 0, the method will return
     * a date string in the DATE_COOKIE format. Other than that, the method
     * will return empty string.
     */
    public function getLifetime() : string {
        if ($this->expires === 0) {
            return '';
        }
        return date(DATE_COOKIE, $this->expires);
    }
    /**
     * Returns the path at which the cookie will operate at.
     * 
     * The Path attribute indicates a URL path that must exist in the requested 
     * URL in order to send the Cookie header.
     * 
     * @return string The path at which the cookie will operate at. Default is '/'
     * which indicates that the cookie will operate in all paths of the domain.
     */
    public function getPath() : string {
        return $this->path;
    }
    /**
     * Checks if the attribute 'Secure' is set or not.
     * 
     * A cookie with the Secure attribute is only sent to the server with an
     * encrypted request over the HTTPS protocol. It's never sent with unsecured
     * HTTP (except on localhost), which means man-in-the-middle attackers
     * can't access it easily.
     * 
     * @return bool If set, the method will return true. False otherwise. Default
     * is true.
     */
    public function isSecure() : bool {
        return $this->secure;
    }
    /**
     * Sets the attribute 'Secure'.
     * 
     * A cookie with the Secure attribute is only sent to the server with an
     * encrypted request over the HTTPS protocol. It's never sent with unsecured
     * HTTP (except on localhost), which means man-in-the-middle attackers
     * can't access it easily.
     * 
     * @param bool $bool True to make the cookie secure only. False to not.
     */
    public function setIsSecure(bool $bool) {
        $this->secure = $bool;
    }
    /**
     * Returns an object that represents http header of the cookie.
     * 
     * @return HttpHeader
     */
    public function getHeader() : HttpHeader {
        return new HttpHeader('set-cookie', $this.'');
    }
    /**
     * Checks if the attribute 'HttpOnly' is set or not.
     * 
     * A cookie with the HttpOnly attribute is inaccessible to the JavaScript
     * Document.cookie API; it's only sent to the server.
     * 
     * @return bool If set, the method will return true. False otherwise. Default
     * is true.
     */
    public function isHttpOnly() : bool {
        return $this->httpOnly;
    }
    /**
     * Sets the attribute 'HttpOnly'.
     * 
     * A cookie with the HttpOnly attribute is inaccessible to the JavaScript
     * Document.cookie API; it's only sent to the server.
     * 
     * @param bool $bool True to make it HttpOnly. false other wise.
     */
    public function setIsHttpOnly(bool $bool) {
        $this->httpOnly = $bool;
    }
    /**
     * Returns the value of the attribute 'SameSite'.
     * 
     * The SameSite attribute lets servers specify whether/when cookies are sent
     * with cross-site requests. This provides some protection against 
     * cross-site request forgery attacks (CSRF).
     * 
     * @return string One of 3 values, 'Lax', 'None' or 'Strict'
     */
    public function getSameSite() : string {
        return $this->sameSite;
    }
    /**
     * Returns a string which can be used to sent the cookie using Http headers.
     * 
     * @return string
     */
    public function getHeaderString() : string {
        $cookieName = $this->getName();
        $cookieVal = $this->getValue();
        $lifetime = $this->getLifetime();
        $expires = $lifetime;
        if ($lifetime != '') {
            $expires = '; expires='.$lifetime;
        }
        $cookiePath = $this->getPath();
        $isSecure = $this->isSecure() ? '; Secure' : '';
        $isHttpOnly = $this->isHttpOnly() ? '; HttpOnly' : '';
        $sameSiteVal = $this->getSameSite();
        return "$cookieName=$cookieVal"
                ."$expires; "
                ."path=".$cookiePath
                ."$isSecure"
                ."$isHttpOnly"
                .'; SameSite='.$sameSiteVal;
    }
    public function __toString() {
        return $this->getHeaderString();
    }
}
