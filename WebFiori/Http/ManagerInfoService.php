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
        parent::__construct('api-info');
        $this->setDescription('Returns a JSON string that contains all '
                .'needed information about all end points which are registered '
                .'under given manager.');
        $this->addParameter([
            ParamOption::NAME => 'version',
            ParamOption::TYPE => ParamType::STRING,
            ParamOption::OPTIONAL => true,
            ParamOption::DESCRIPTION => 'Optional parameter. '
                .'If set, the information that will be returned will be specific '
                .'to the given version number.'
        ]);
        $this->setRequestMethods(RequestMethod::GET, RequestMethod::POST);
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
