<?php
namespace restEasy\tests;
use restEasy\RequestParameter;
use restEasy\APIAction;
use restEasy\WebAPI;
/**
 * Description of SampleAPI
 *
 * @author Eng.Ibrahim
 */
class SampleAPI extends WebAPI{
    public function __construct() {
        parent::__construct();
        $a00 = new APIAction('add-two-integers');
        $a00->setDescription('Returns a JSON string that has the sum of two integers.');
        $a00->addRequestMethod('get');
        $a00->addParameter(new RequestParameter('first-number', 'integer'));
        $a00->addParameter(new RequestParameter('second-number', 'integer'));
        $this->addAction($a00);
        
        $this->setVersion('1.0.1');
        $a01 = new APIAction('sum-array');
        $a01->addRequestMethod('post');
        $a01->addRequestMethod('get');
        $a01->setDescription('Returns a JSON string that has the sum of array of numbers.');
        $a01->addParameter(new RequestParameter('numbers', 'array'));
        $this->addAction($a01,true);
        
        $a02 = new APIAction('get-user-profile');
        $a02->addRequestMethod('post');
        $a02->setDescription('Returns a JSON string that has user profile info.');
        $a02->addParameter(new RequestParameter('user-id', 'integer'));
        $this->addAction($a02,true);
        
        $a03 = new APIAction('do-nothing');
        $a03->addRequestMethod('get');
        $a03->addRequestMethod('post');
        $a03->addRequestMethod('put');
        $a03->addRequestMethod('delete');
        $this->addAction($a03,true);
    }
    /**
     * 
     * @return boolean True if $_GET['pass'] or $_POST['pass'] is equal to 123
     */
    public function isAuthorized() {
        $pass = isset($_GET['pass']) ? $_GET['pass'] : null;
        if($pass == null){
            $pass = isset($_POST['pass']) ? $_POST['pass'] : null;
        }
        if($pass == '123'){
            return true;
        }
        return false;
    }

    public function processRequest() {
        $action = $this->getAction();
        $i = $this->getInputs();
        if($action == 'add-two-integers'){
            $sum = $i['first-number'] + $i['second-number'];
            $this->sendResponse('The sum of '.$i['first-number'].' and '.$i['second-number'].' is '.$sum.'.');
        }
        else if($action == 'sum-array'){
            $sum = 0;
            foreach ($i['numbers'] as $num){
                if(gettype($num) == 'integer' || gettype($num) == 'double'){
                    $sum += $num;
                }
            }
            $j = new \jsonx\JsonX();
            $j->add('sum', $sum);
            echo $j;
        }
        else if($action == 'get-user-profile'){
            if($i['user-id'] <= 0){
                $this->databaseErr();
            }
            else{
                $j = new \jsonx\JsonX();
                $j->add('user-name', 'Ibrahim');
                $j->add('bio', 'A software engineer who is ready to help anyone in need.');
                echo $j;
            }
        }
        else{
            $this->actionNotImpl();
        }
    }

}
