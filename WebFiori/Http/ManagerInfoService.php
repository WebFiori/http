<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

/**
 * A service which can be used to display information about services manager.
 * 
 * The developer must extend this class and complete the implementation of 
 * the method WebService::isAuthorized() in order to use it.
 * 
 * @author Ibrahim
 * 
 */
abstract class ManagerInfoService extends WebService {
    /**
     * Creates new instance of the class.
     * 
     */
    public function __construct() {
        parent::__construct('api-docs');
        $this->setDescription('Returns a JSON string that contains all '
                .'needed information about all end points which are registered '
                .'under given manager.');
        $this->addRequestMethod(RequestMethod::GET);
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
