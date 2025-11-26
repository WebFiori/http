<?php

namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\RequestMethod;
use WebFiori\Http\RequestParameter;
use WebFiori\Json\Json;
/**
 * Description of GetUserProfileService
 *
 * @author Ibrahim
 */
class GetUserProfileService extends AbstractNumbersService {
    public function __construct() {
        parent::__construct('get-user-profile');
        $this->addRequestMethod(RequestMethod::POST);
        $this->setDescription('Returns a JSON string that has user profile info.');
        $this->addParameter(new RequestParameter('user-id', 'integer'));
    }
    public function processRequest() {
        $userId = $this->getParamVal('user-id');
        if ($userId === null || $userId < 0) {
            $this->sendResponse('Database Error.', 500, self::E);
        } else {
            $j = new Json();
            $j->add('user-name', 'Ibrahim');
            $j->add('bio', 'A software engineer who is ready to help anyone in need.');
            $this->send('application/json', $j);
        }
    }

}
