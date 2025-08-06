<?php

namespace WebFiori\Tests\Http\TestServices;

use Exception;
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
class CreateUserProfileService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('create-user-profile');
        $this->addRequestMethod(RequestMethod::POST);
        $this->addParameter(new RequestParameter('id', 'integer'));
        $this->getParameterByName('id')->setIsOptional(true);
        $this->addParameters([
            'name' => [
                ParamOption::TYPE => ParamType::STRING
            ],
            'username' => [
                ParamOption::TYPE => ParamType::STRING
            ],
            'x' => [
                ParamOption::TYPE => ParamType::INT,
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
