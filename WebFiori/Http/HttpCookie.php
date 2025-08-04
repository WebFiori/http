<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2022 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * A class which is used to represent Http cookies.
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
    private $cookieName;
    private $domain;
    private $expires;
    private $httpOnly;
    private $path;
    private $sameSite;
    private $secure;
    private $val;
    /**
     * Creates new instance of the class with default properties.
     * 
     * A newly created cookie will have the following properties by default:
     * <ul>
     * <li>name: new-cookie</li>
     * <li>path: /</li>
     * <li>secure: true</li>
     * <li>http only: true</li>
     * <li>domain: The domain at which the library is operating from.</li>
     * <li>same site: Lax</li>
     * <li>expires: 0</li>
     * <li>value: sha256 hash</li>
     * </ul>
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
    public function __toString() {
        return $this->getHeaderString();
    }
    /**
     * Returns the domain at which the cookie will operate from.
     * 
     * @return string|null If the domain is set, the method will return it
     * as string. Other than that, null is returned.
     */
    public function getDomain() {
        return $this->domain;
    }
    /**
     * Returns the time at which the session is set to expire at in seconds.
     * 
     * @return int If the returned value is 0, this means that the expiry
     * time of the session is not set. other than that, the returned value
     * will represent the time at which the cookie will expire at.
     */
    public function getExpires() : int {
        return $this->expires;
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
     * Returns a string which can be used to send the cookie using Http headers.
     * 
     * @return string
     */
    public function getHeaderString() : string {
        $headerArr = [
            $this->getName().'='.$this->getValue()
        ];
        $lifetime = $this->getLifetime();

        if ($lifetime != '') {
            $headerArr[] = 'expires='.$lifetime;
        }

        if ($this->getDomain() !== null) {
            $headerArr[] = 'domain='.$this->getDomain();
        }
        $headerArr[] = 'path='.$this->getPath();

        if ($this->isSecure()) {
            $headerArr[] = 'Secure';
        }

        if ($this->isHttpOnly()) {
            $headerArr[] = 'HttpOnly';
        }
        $headerArr[] = 'SameSite='.$this->getSameSite();

        return implode('; ', $headerArr);
    }
    /**
     * Returns a string that represents the time at which the cookie will
     * expire at.
     * 
     * @return string If the expires is a value is not 0, the method will return
     * a date string in the DATE_COOKIE format. Other than that, the method
     * will return empty string.
     */
    public function getLifetime() : string {
        if ($this->expires == 0) {
            return '';
        }

        return date(DATE_COOKIE, $this->expires);
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
     * Returns number of seconds before the cookie expires.
     * 
     * @return int If the cookie is non-persistent or the cookie has expired,
     * the method will always return 0. Other than that, the method will return
     * number of seconds remaining before the cookie dies.
     */
    public function getRemainingTime() : int {
        $expiresAt = $this->getExpires();

        if ($expiresAt == 0) {
            return 0;
        }
        $remaining = $expiresAt - time();

        if ($remaining < 0) {
            return 0;
        }

        return $remaining;
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
     * Returns a string that represents the name of the cookie.
     * 
     * @return string A string that represents the name of the cookie. Default
     * return value is a random hash.
     */
    public function getValue() : string {
        return $this->val;
    }
    /**
     * Checks if the attribute 'HttpOnly' is set or not.
     * 
     * A cookie with the HttpOnly attribute is inaccessible to the JavaScript's
     * Document Cookie API; it's only sent to the server.
     * 
     * @return bool If set, the method will return true. False otherwise. Default
     * is true.
     */
    public function isHttpOnly() : bool {
        return $this->httpOnly;
    }
    /**
     * Checks if the cookie is persistent or not.
     * 
     * A cookie is considered as persistent if the 'expire' attribute of the
     * cookie is set. It simply means that the cookie will be not removed
     * even if the browser is closed unless the expiry time has passed.
     * 
     * @return bool If persistent, true is returned. False otherwise.
     */
    public function isPersistent() : bool {
        return $this->getLifetime() != '';
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
     * Kill the cookie.
     * 
     * Killing a cookie is simply setting its expiry to a negative value which
     * simply indicates a date in the past.
     */
    public function kill() {
        $this->expires = time() - 60 * 60 * 24;
    }
    /**
     * Sets the domain that the cookie will operate from.
     * 
     * If empty string is provided, this means that the domain attribute will be
     * not included in cookie header.
     * 
     * @param string $domain Domain name such as example.com
     */
    public function setDomain(string $domain = '') {
        $trimmed = trim($domain);

        if (strlen($trimmed) == 0) {
            $this->domain = null;
        } else {
            $this->domain = $trimmed;
        }
    }
    /**
     * Sets cookie duration.
     * 
     * @param float $expireAfter Cookie duration in minutes. If 0 is given,
     * the expiry attribute will not be included.
     *
     */
    public function setExpires(float $expireAfter) {
        if ($expireAfter == 0) {
            $this->expires = 0;

            return;
        }
        $this->expires = time() + $expireAfter * 60;
    }
    /**
     * Sets the attribute 'HttpOnly'.
     * 
     * A cookie with the HttpOnly attribute is inaccessible to the JavaScript's
     * Document cookie API; it's only sent to the server.
     * 
     * @param bool $bool True to make it HttpOnly. false otherwise.
     */
    public function setIsHttpOnly(bool $bool) {
        $this->httpOnly = $bool;
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
}
