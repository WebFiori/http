<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use InvalidArgumentException;

/**
 * A class for representing and parsing URIs.
 *
 * @author Ibrahim
 */
class Uri {
    /**
     * An array that contains URI parts.
     * 
     * @var array
     */
    private $uriBroken;
    
    /**
     * Creates new instance of the class.
     * 
     * @param string $requestedUri The URI such as 'https://www3.programmingacademia.com:80/{some-var}/hell/{other-var}/?do=dnt&y=2018#xyz'
     * 
     * @throws InvalidArgumentException
     */
    public function __construct(string $requestedUri = '') {
        if (strlen(trim($requestedUri)) == 0) {
            $this->uriBroken = [
                'uri' => $requestedUri,
                'port' => '',
                'host' => '',
                'authority' => '',
                'scheme' => '',
                'query-string' => '',
                'fragment' => '',
                'path' => [],
                'query-string-vars' => [],
                'uri-vars' => []
            ];
        } else {
            $this->uriBroken = self::splitURI($requestedUri);
            
            if ($this->uriBroken === false) {
                throw new InvalidArgumentException('Invalid URI: \''.$requestedUri.'\'.');
            }
        }
    }
    
    /**
     * Returns the authority part of the URI.
     * 
     * @return string The authority part of the URI. Usually, 
     * it is the sub-domain + domain name + port number.
     */
    public function getAuthority() : string {
        return $this->uriBroken['authority'];
    }
    
    /**
     * Returns the base URL of the framework.
     * 
     * @return string The base URL (such as 'https://example.com/' or 'http://127.0.0.1/')
     */
    public static function getBaseURL() : string {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $useHttps = !empty($_SERVER['HTTPS']) && !defined('USE_HTTP');
        $protocol = $useHttps ? 'https://' : 'http://';
        
        if (defined('ROOT_PATH')) {
            $path = ROOT_PATH;
        } else {
            $path = '';
        }
        
        return $protocol . $host . $path;
    }
    
    /**
     * Returns an array that contains all URI parts.
     * 
     * @return array The method will return an associative array that 
     * contains the components of the URI. The array will have the 
     * following indices:
     * <ul>
     * <li><b>uri</b>: The original URI.</li>
     * <li><b>port</b>: The port number taken from the authority part.</li>
     * <li><b>host</b>: FQDN taken from the authority part.</li>
     * <li><b>authority</b>: Authority part of the URI.</li>
     * <li><b>scheme</b>: Scheme part of the URI (e.g. http or https).</li>
     * <li><b>query-string</b>: Query string if the URI has any.</li>
     * <li><b>fragment</b>: Any string that comes after the character '#' in the URI.</li>
     * <li><b>path</b>: An array that contains the names of path directories</li>
     * <li><b>query-string-vars</b>: An array that contains query string parameter and values.</li>
     * <li><b>uri-vars</b>: An array that contains URI parameters (e.g. {id}, {name}, etc...).</li>
     * </ul>
     */
    public function getComponents() : array {
        return $this->uriBroken;
    }
    
    /**
     * Returns the fragment part of the URI.
     * 
     * @return string The fragment part of the URI. The fragment in the URI is 
     * any string that comes after the character '#'.
     */
    public function getFragment() : string {
        return $this->uriBroken['fragment'];
    }
    
    /**
     * Returns the host name from the authority part of the URI.
     * 
     * @return string The host name such as 'www.example.com'.
     */
    public function getHost() : string {
        return $this->uriBroken['host'];
    }
    /**
     * Returns an array which contains the names of URI directories.
     * 
     * @return array An array which contains the names of URI directories. 
     * For example, if the path part of the URI is '/path1/path2', the 
     * array will contain the value 'path1' at index 0 and 'path2' at index 1.
     * 
     */
    public function getPathArray() : array {
        return $this->uriBroken['path'];
    }
    /**
     * Returns the path part of the URI.
     * 
     * @return string A string such as '/path1/path2/path3'.
     */
    public function getPath() : string {
        $path = $this->uriBroken['path'];
        
        if (count($path) == 0) {
            return '/';
        }
        
        return '/'.implode('/', $path);
    }
    
    /**
     * Returns the port number of the authority part of the URI.
     * 
     * @return string The port number such as '80' or '443'. If no port 
     * number is specified, the method will return empty string.
     */
    public function getPort() : string {
        return $this->uriBroken['port'];
    }
    
    /**
     * Returns the query string that was appended to the URI.
     * 
     * @return string The query string such as 'do=dnt&y=2018'. If the URI 
     * has no query string, the method will return empty string.
     */
    public function getQueryString() : string {
        return $this->uriBroken['query-string'];
    }
    
    /**
     * Returns an associative array which contains query string parameters.
     * 
     * @return array An associative array. The keys will be parameters 
     * names and the values are parameters values. If the URI has no query 
     * string, the method will return empty array.
     */
    public function getQueryStringVars() : array {
        return $this->uriBroken['query-string-vars'];
    }
    
    /**
     * Returns the scheme part of the URI.
     * 
     * @return string The scheme part of the URI. Usually, it can be 
     * 'http' or 'https'.
     */
    public function getScheme() : string {
        return $this->uriBroken['scheme'];
    }
    
    /**
     * Returns the original requested URI.
     * 
     * @return string The original requested URI.
     */
    public function getUri() : string {
        return $this->uriBroken['uri'];
    }
    
    /**
     * Splits a URI into its basic components.
     * 
     * @param string $uri The URI that will be split.
     * 
     * @return array|bool If the given URI is not valid, 
     * the method will return false. Other than that, The method will return an associative array that 
     * contains the components of the URI.
     */
    public static function splitURI(string $uri) {
        $components = parse_url(str_replace(' ', '%20', $uri));
        
        if ($components === false) {
            return false;
        }
        
        $retVal = [
            'uri' => $uri,
            'authority' => '',
            'host' => $components['host'] ?? '',
            'port' => isset($components['port']) ? (string)$components['port'] : '',
            'scheme' => $components['scheme'] ?? '',
            'query-string' => $components['query'] ?? '',
            'fragment' => $components['fragment'] ?? '',
            'path' => [],
            'query-string-vars' => [],
            'uri-vars' => [],
        ];
        
        // Build authority
        if (!empty($retVal['host'])) {
            $retVal['authority'] = '//' . $retVal['host'];
            if (!empty($retVal['port'])) {
                $retVal['authority'] .= ':' . $retVal['port'];
            }
        }
        
        // Parse path and extract parameters
        if (isset($components['path'])) {
            $pathParts = explode('/', trim($components['path'], '/'));
            $addedParams = [];
            
            foreach ($pathParts as $part) {
                if ($part !== '') {
                    $retVal['path'][] = mb_convert_encoding(urldecode($part), 'UTF-8', 'ISO-8859-1');
                    
                    if ($part[0] === '{' && $part[strlen($part) - 1] === '}') {
                        $name = trim($part, '{}');
                        if (!isset($addedParams[$name])) {
                            $addedParams[$name] = true;
                            $retVal['uri-vars'][] = new UriParameter($name);
                        }
                    }
                }
            }
        }
        
        // Parse query string
        if (!empty($retVal['query-string'])) {
            parse_str($retVal['query-string'], $retVal['query-string-vars']);
        }
        
        return $retVal;
    }
}
