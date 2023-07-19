<?php

namespace webfiori\tests\http\testServices;

use webfiori\http\RequestParameter;
use webfiori\json\Json;
use Exception;
/**
 * Description of CreateUserProfileService
 *
 * @author Ibrahim
 */
class CreateUserProfileService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('create-user-profile');
        $this->addRequestMethod('post');
        $this->addParameter(new RequestParameter('id', 'integer'));
        $this->getParameterByName('id')->setIsOptional(true);
        $this->addParameters([
            'name' => [
                'type' => 'string'
            ],
            'username' => [
                'type' => 'string'
            ],
            'x' => [
                'type' => 'int',
                'optional' => true,
                'default' => 3
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
