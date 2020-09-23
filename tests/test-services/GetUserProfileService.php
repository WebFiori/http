<?php

namespace restEasy\tests;

use webfiori\restEasy\RequestParameter;
use webfiori\json\Json;
/**
 * Description of GetUserProfileService
 *
 * @author Ibrahim
 */
class GetUserProfileService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('get-user-profile');
        $this->addRequestMethod('post');
        $this->setDescription('Returns a JSON string that has user profile info.');
        $this->addParameter(new RequestParameter('user-id', 'integer'));
    }
    public function processRequest($inputs) {
        if ($inputs['user-id'] <= 0) {
            $this->getManager()->databaseErr();
        } else {
            $j = new Json();
            $j->add('user-name', 'Ibrahim');
            $j->add('bio', 'A software engineer who is ready to help anyone in need.');
            $this->send('application/json', $j);
        }
    }

}
