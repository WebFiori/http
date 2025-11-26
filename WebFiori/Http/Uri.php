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
            throw new InvalidArgumentException('URI must be non-empty string');
        } else {
            $this->uriBroken = self::splitURI($requestedUri);
            
            if ($this->uriBroken === false) {
                throw new InvalidArgumentException('Invalid URI: \''.$requestedUri.'\'');
            }
        }
    }
    /**
     * Returns the original requested URI.
     * 
     * @param boolean $incQueryStr If set to true, the query string part 
     * will be included in the URL. Default is false.
     * 
     * @param boolean $incFragment If set to true, the fragment part 
     * will be included in the URL. Default is false.
     * 
     * @return string The original requested URI.
     * 
     */
    public function getUri(bool $incQueryStr = false, bool $incFragment = false) : string {
        $retVal = $this->getScheme().':'.$this->getAuthority().$this->getPath();

        if ($incQueryStr === true && $incFragment === true) {
            $queryStr = $this->getQueryString();

            if (strlen($queryStr) != 0) {
                $retVal .= '?'.$queryStr;
            }
            $fragment = $this->getFragment();

            if (strlen($fragment) != 0) {
                $retVal .= '#'.$fragment;
            }
        } else {
            if ($incQueryStr === true && $incFragment === false) {
                $queryStr = $this->getQueryString();

                if (strlen($queryStr) != 0) {
                    $retVal .= '?'.$queryStr;
                }
            } else {
                if ($incQueryStr === false && $incFragment === true) {
                    $fragment = $this->getFragment();

                    if (strlen($fragment) != 0) {
                        $retVal .= '#'.$fragment;
                    }
                }
            }
        }

        return $retVal;
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
        $tempHost = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
        $host = trim(filter_var($tempHost),'/');

        if (isset($_SERVER['HTTPS'])) {
            $secureHost = filter_var($_SERVER['HTTPS']);
        } else {
            $secureHost = '';
        }
        $protocol = 'http://';
        $useHttp = defined('USE_HTTP') && USE_HTTP === true;

        if (strlen($secureHost) != 0 && !$useHttp) {
            $protocol = "https://";
        }

        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = filter_var($_SERVER['DOCUMENT_ROOT']);
        } else {
            //Fix for IIS since the $_SERVER['DOCUMENT_ROOT'] is not set
            //in some cases
            $docRoot = getcwd();
        }

        $docRootLen = strlen($docRoot);

        if ($docRootLen == 0) {
            $docRoot = __DIR__;
            $docRootLen = strlen($docRoot);
        }

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', __DIR__);
        }
        $toAppend = str_replace('\\', '/', substr(ROOT_PATH, $docRootLen, strlen(ROOT_PATH) - $docRootLen));

        if (defined('WF_PATH_TO_REMOVE')) {
            $toAppend = str_replace(str_replace('\\', '/', WF_PATH_TO_REMOVE),'' ,$toAppend);
        }
        $xToAppend = str_replace('\\', '/', $toAppend);

        if (defined('WF_PATH_TO_APPEND')) {
            $xToAppend = $xToAppend.'/'.trim(str_replace('\\', '/', WF_PATH_TO_APPEND), '/');
        }

        if (strlen($xToAppend) == 0) {
            return $protocol.$host;
        } else {
            return $protocol.$host.'/'.trim($xToAppend, '/');
        }
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
     * Splits a string based on character mask.
     * 
     * @param string $split The string to split.
     * 
     * @param string $char The character that the split is based on.
     * 
     * @param string $encoded The character when encoded in URI.
     * 
     * @return array
     */
    private static function _queryOrFragment(string $split, string $char, string $encoded) : array {
        $split2 = explode($char, $split);
        $spCount = count($split2);

        if ($spCount > 2) {
            $temp = [];

            for ($x = 0 ; $x < $spCount - 1 ; $x++) {
                $temp[] = $split2[$x];
            }
            $lastStr = $split2[$spCount - 1];

            if (strlen($lastStr) == 0) {
                $split2 = [
                    implode($encoded, $temp).$encoded
                ];
            } else {
                $split2 = [
                    implode($encoded, $temp),
                    $split2[$spCount - 1]
                ];
            }
        }

        return $split2;
    }
    public function equals(Uri $uri) : bool {
        return $this->getUri(true, true) == $uri->getUri(true, true);
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
        $validate = filter_var(str_replace(' ', '%20', $uri),FILTER_VALIDATE_URL);

        if ($validate === false) {
            return false;
        }
        $retVal = [
            'uri' => $uri,
            'authority' => '',
            'host' => '',
            'port' => '',
            'scheme' => '',
            'query-string' => '',
            'fragment' => '',
            'path' => [],
            'query-string-vars' => [

            ],
            'uri-vars' => [

            ],
        ];
        //First step, extract the fragment
        $split1 = self::_queryOrFragment($uri, '#', '%23');
        $retVal['fragment'] = $split1[1] ?? '';

        //after that, extract the query string
        $split1[0] = str_replace('?}', '<>', $split1[0]);
        $split2 = self::_queryOrFragment($split1[0], '?', '%3F');

        $retVal['query-string'] = $split2[1] ?? '';

        $split2[0] = str_replace('<>', '?}', $split2[0]);
        //next comes the scheme
        $split3 = explode(':', $split2[0]);
        $retVal['scheme'] = $split3[0];

        if (count($split3) == 3) {
            //if 3, this means port number was specified in the URI
            $split3[1] = $split3[1].':'.$split3[2];
        }
        //now, break the remaining using / as a delimiter
        //the authority will be located at index 2 if the URI
        //follows the standard
        $split4 = explode('/', $split3[1]);
        $retVal['authority'] = '//'.$split4[2];

        //after that, we create the path from the remaining parts
        //also we check if the path has parameters or not
        //a parameter is a value in the path which is enclosed between {}
        //optional parameter ends with ? (e.g. {name?}
        $addedParams = [];

        for ($x = 3 ; $x < count($split4) ; $x++) {
            $dirName = $split4[$x];

            if ($dirName != '') {
                $retVal['path'][] = mb_convert_encoding(urldecode($dirName), 'UTF-8', 'ISO-8859-1');
            }
        }
        //now extract port number from the authority (if any)
        $split5 = explode(':', $retVal['authority']);
        $retVal['port'] = $split5[1] ?? '';
        //Also, host can be extracted at this step.
        $retVal['host'] = trim($split5[0],'/');
        //finally, split query string and extract vars
        $split6 = explode('&', $retVal['query-string']);

        foreach ($split6 as $param) {
            $split7 = explode('=', $param);
            $retVal['query-string-vars'][$split7[0]] = $split7[1] ?? '';
        }

        return $retVal;
    }
}
