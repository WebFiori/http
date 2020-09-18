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
class SampleService extends WebServicesManager {
    public function __construct() {
        parent::__construct();
        
        $this->addService(new AddNubmersService());

        $this->setVersion('1.0.1');
        
        $this->addService(new SumNumbersService());

        $a02 = new TestServiceObj('get-user-profile');
        $a02->addRequestMethod('post');
        $a02->setDescription('Returns a JSON string that has user profile info.');
        $a02->addParameter(new RequestParameter('user-id', 'integer'));
        $this->addService($a02,true);

        $a03 = new TestServiceObj('do-nothing');
        $a03->addRequestMethod('get');
        $a03->addRequestMethod('post');
        $a03->addRequestMethod('put');
        $a03->addRequestMethod('delete');
        $this->addService($a03,true);
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
                
            } else {
                if ($action == 'get-user-profile') {
                    if ($i['user-id'] <= 0) {
                        $this->databaseErr();
                    } else {
                        $j = new Json();
                        $j->add('user-name', 'Ibrahim');
                        $j->add('bio', 'A software engineer who is ready to help anyone in need.');
                        $this->send('application/json', $j);
                    }
                } else {
                    $this->serviceNotImplemented();
                }
            }
        }
    }
}
