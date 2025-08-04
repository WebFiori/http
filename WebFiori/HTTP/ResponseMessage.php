<?php
namespace webfiori\http;

/**
 * A class which is used to keep track of values of default response messages.
 *
 * The library supports following default status messages.
 * <ul>
 * <li>'401': 'Not Authorized.'</li>
 * <li>'404-1': 'The following parameter(s) has invalid values: '</li>
 * <li>'404-2': 'The following required parameter(s) where missing from the request body: '</li>
 * <li>'404-3': 'Service name is not set.'</li>
 * <li>'404-4': 'Service not implemented.'</li>
 * <li>'404-5': 'Service not supported.'</li>
 * <li>'404-6': 'Request method(s) of the service are not set in code.'</li>
 * <li>'405': 'Method Not Allowed.'</li>
 * <li>'415': 'Content type not supported.'</li>
 * 
 * @author Ibrahim
 */
class ResponseMessage {
    private static $inst;
    private $messages;
    private function __construct() {
        $this->messages = [
            '401' => 'Not Authorized.',
            '404-1' => 'The following parameter(s) has invalid values: ',
            '404-2' => 'The following required parameter(s) where missing from the request body: ',
            '404-3' => 'Service name is not set.',
            '404-4' => 'Service not implemented.',
            '404-5' => 'Service not supported.',
            '404-6' => 'Request methods of the service are not set in code.',
            '405' => 'Method Not Allowed.',
            '415' => 'Content type not supported.'
        ];
    }
    /**
     * Returns the value of response message given its code.
     * 
     * @param string $code The code of the message such as 415.
     * 
     * @return string If the code has an error message set, the method will
     * return it. Other than that, the string '-' is returned.
     */
    public static function get(string $code) : string {
        $tr = trim($code);

        if (isset(self::getInstance()->messages[$tr])) {
            return self::getInstance()->messages[$tr];
        }

        return '-';
    }
    /**
     * Sets a custom HTTP response message for specific error code.
     * 
     * This method is used to customize the message that will be sent back to
     * the client in case of using a method such as WebServicesManager::notAuth().
     * Also, this method can be used to add custom code with error message.
     * 
     * @param string $code A string that represent the error code. By default,
     * the class has the following error codes pre-defined:
     * <ul>
     * <li>'401': 'Not Authorized.'</li>
     * <li>'404-1': 'The following parameter(s) has invalid values: '</li>
     * <li>'404-2': 'The following required parameter(s) where missing from the request body: '</li>
     * <li>'404-3': 'Service name is not set.'</li>
     * <li>'404-4': 'Service not implemented.'</li>
     * <li>'404-5': 'Service not supported.'</li>
     * <li>'404-6': 'Request method(s) of the service are not set in code.'</li>
     * <li>'405': 'Method Not Allowed.'</li>
     * <li>'415': 'Content type not supported.'</li>
     * </ul>
     * 
     * @param string $message A string that represents the error message.
     */
    public static function set(string $code, string $message) {
        self::getInstance()->messages[trim($code)] = $message;
    }
    /**
     * 
     * @return ResponseMessage
     */
    private static function getInstance() : ResponseMessage {
        if (self::$inst === null) {
            self::$inst = new ResponseMessage();
        }

        return self::$inst;
    }
}
