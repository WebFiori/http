<?php

namespace webfiori\tests\http\testServices;

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
        $this->addRequestMethod('post');
        $this->addParameter(new RequestParameter('id', 'integer'));
        $this->addParameters([
            'name' => [
                'type' => 'string'
            ],
            'username' => [
                'type' => 'string'
            ]
        ]);
    }
    public function processRequest() {
        $userObj = $this->getObject(TestUserObj::class, [
            'name' => 'setFullName'
        ]);
            $j = new Json();
            $j->addObject('user', $userObj);
            $this->send('application/json', $j);
    }

}
