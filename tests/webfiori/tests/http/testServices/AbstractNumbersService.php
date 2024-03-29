<?php
namespace webfiori\tests\http\testServices;

use webfiori\http\AbstractWebService;
use webfiori\http\RequestParameter;

/**
 * Description of AbstractNumbersService
 *
 * @author Ibrahim
 */
abstract class AbstractNumbersService extends AbstractWebService {
    public function __construct($name) {
        parent::__construct($name);
        $this->addParameter(new RequestParameter('pass','string'));
    }
    public function isAuthorized() {
        $inputs = $this->getInputs();
        if ($inputs instanceof \webfiori\json\Json) {
            $pass = $inputs->get('pass');
        } else {
            $pass = isset($inputs['pass']) ? $inputs['pass'] : null;
        }

        if ($pass == null) {
            $pass = isset($inputs['pass']) ? $inputs['pass'] : null;
        }

        if ($pass == '123') {
            return true;
        }

        return false;
    }

}









