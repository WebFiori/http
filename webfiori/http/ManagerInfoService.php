<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2020 Ibrahim BinAlshikh
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace webfiori\http;

/**
 * A service which can be used to display information about services manager.
 * 
 * The developer must extend this class and complete the implementation of 
 * the method AbstractWebService::isAuthorized() in order to use it.
 * 
 * @author Ibrahim
 * 
 * @version 1.0
 */
abstract class ManagerInfoService extends AbstractWebService {
    /**
     * Creates new instance of the class.
     * 
     * @since 1.0
     */
    public function __construct() {
        parent::__construct('api-info');
        $this->setDescription('Returns a JSON string that contains all '
                .'needed information about all end points which are registered '
                .'under given manager.');
        $this->addParameter([
            'name' => 'version',
            'type' => 'string',
            'optional' => true,
            'description' => 'Optional parameter. '
                .'If set, the information that will be returned will be specific '
                .'to the given version number.'
        ]);
        $this->addRequestMethod('get');
        $this->addRequestMethod('post');
    }
    /**
     * Sends back JSON response that contains information about the services 
     * at which the manager which is associated with the instance manages.
     * 
     */
    public function processRequest() {
        $this->send('application/json', $this->getManager()->toJSON());
    }
}
