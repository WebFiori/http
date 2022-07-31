<?php

namespace webfiori\tests\http\testServices;

use webfiori\http\RequestParameter;
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
    public function processRequest() {
        $userId = $this->getParamVal('user-id');
        if ($userId === null || $userId < 0) {
            $this->getManager()->sendResponse('Database Error.', self::E, 500);
        } else {
            $j = new Json();
            $j->add('user-name', 'Ibrahim');
            $j->add('bio', 'A software engineer who is ready to help anyone in need.');
            $this->send('application/json', $j);
        }
    }

}
