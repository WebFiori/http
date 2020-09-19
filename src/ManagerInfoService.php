<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace webfiori\restEasy;

/**
 * Description of SetInfoService
 *
 * @author Ibrahim
 */
class ManagerInfoService extends AbstractWebService {
    public function __construct() {
        parent::__construct('api-info');
        $this->setDescription('Returns a JSON string that contains all '
                . 'needed information about all end points which are registered '
                . 'under given manager.');
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

    public function isAuthorized() {
        // TODO: Check if user is authorized to call the service or not.
    }

    public function processRequest($inputs) {
        $this->send('application/json', $this->getManager()->toJSON());
    }

}
