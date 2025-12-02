<?php

namespace WebFiori\Tests\Http\TestServices;

use Exception;
use WebFiori\Http\WebService;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;
use WebFiori\Http\RequestMethod;
use WebFiori\Http\RequestParameter;
use WebFiori\Json\Json;
/**
 * Description of CreateUserProfileService
 *
 * @author Ibrahim
 */
class CreateUserProfileServiceV2 extends WebService {
    public function __construct() {
        parent::__construct('user-profile');
        $this->addRequestMethod(RequestMethod::POST);
        $this->addRequestMethod(RequestMethod::GET);
        $this->addParameters([
            'id' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::METHODS => [RequestMethod::GET]
            ],
            'name' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::METHODS => [RequestMethod::POST]
            ],
            'username' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::METHODS => [RequestMethod::POST]
            ],
            'x' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true,
                ParamOption::DEFAULT => 3
            ]
        ]);
    }
    public function processRequest() {
        
    }
    public function processGET() {
            $j = new Json();
            $userObj = new TestUserObj();
            $userObj->setFullName('Ibx');
            $userObj->setId($this->getParamVal('id'));
            $j->addObject('user', $userObj);
            $this->send('application/json', $j);
    }
    public function processPOST() {
        try {
            $userObj = $this->getObject('not\\Exist', [
                'name' => 'setFullName'
            ]);
        } catch (Exception $ex) {
            $userObj = $this->getObject(TestUserObj::class, [
                'name' => 'setFullName',
                'x' => 'setId'
            ]);
        }
            $j = new Json();
            $j->addObject('user', $userObj);
            $this->send('application/json', $j);
    }

}
