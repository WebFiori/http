<?php

namespace webfiori\tests\http\testServices;

use Exception;
use webfiori\http\ParamOption;
use webfiori\http\ParamTypes;
use webfiori\http\RequestMethod;
use webfiori\http\RequestParameter;
use webfiori\json\Json;
/**
 * Description of CreateUserProfileService
 *
 * @author Ibrahim
 */
class CreateUserProfileService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('create-user-profile');
        $this->addRequestMethod(RequestMethod::POST);
        $this->addParameter(new RequestParameter('id', 'integer'));
        $this->getParameterByName('id')->setIsOptional(true);
        $this->addParameters([
            'name' => [
                ParamOption::TYPE => ParamTypes::STRING
            ],
            'username' => [
                ParamOption::TYPE => ParamTypes::STRING
            ],
            'x' => [
                ParamOption::TYPE => ParamTypes::INT,
                ParamOption::OPTIONAL => true,
                ParamOption::DEFAULT => 3
            ]
        ]);
    }
    public function processRequest() {
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
