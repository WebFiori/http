<?php
namespace restEasy\tests;

use webfiori\json\Json;
use webfiori\restEasy\RequestParameter;
use webfiori\restEasy\WebServicesManager;
/**
 * Description of SampleAPI
 *
 * @author Eng.Ibrahim
 */
class SampleServicesManager extends WebServicesManager {
    public function __construct() {
        parent::__construct();
        
        $this->addService(new AddNubmersService());

        $this->setVersion('1.0.1');
        
        $this->addService(new SumNumbersService());
        $this->addService(new GetUserProfileService());
    }
    /**
     * 
     * @return boolean True if $_GET['pass'] or $_POST['pass'] is equal to 123
     */
    public function isAuthorized() {
        $pass = isset($_GET['pass']) ? $_GET['pass'] : null;

        if ($pass == null) {
            $pass = isset($_POST['pass']) ? $_POST['pass'] : null;
        }

        if ($pass == '123') {
            return true;
        }

        return false;
    }

    public function processRequest() {
        $action = $this->getAction();
        $i = $this->getInputs();

        if ($action == 'add-two-integers') {
            $sum = $i['first-number'] + $i['second-number'];
            $this->sendResponse('The sum of '.$i['first-number'].' and '.$i['second-number'].' is '.$sum.'.');
        } else {
            if ($action == 'sum-array') {
                
            } else if ($action == 'get-user-profile') {

            } else {
                $this->serviceNotImplemented();
            }
        }
    }
}
