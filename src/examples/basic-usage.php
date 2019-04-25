<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require_once '../jsonx/JsonX.php';
require_once '../jsonx/JsonI.php';
require_once '../WebAPI.php';
require_once '../APIAction.php';
require_once '../APIFilter.php';
require_once '../RequestParameter.php';
use restEasy\WebAPI;
use restEasy\APIAction;
use restEasy\RequestParameter;
/*
 * Steps for creating new API:
 * 1- Create a class that extends the class 'API'.
 * 2- Implement 'isAuthorized()' function.
 * 3- Implement 'processRequest()' function.
 * 4- Create an instance of the class.
 * 5- Call the function 'process()'.
 */
class MyAPI extends WebAPI{
    
    public function __construct() {
        parent::__construct();
        //customize the API as you need here.
        //add actions, parameters for 'GET' or 'POST' or any other request method.
        
        //create new action
        $action00 = new APIAction('my-action');
        $action00->addRequestMethod('get');
        
        //add parameters for the action
        $action00->addParameter(new RequestParameter('my-param', 'string', true));
        
        //add the action to the API
        $this->addAction($action00);
        
        //create another action which requires permissions
        $action01 = new APIAction('auth-action');
        $action01->addRequestMethod('get');
        $action01->addRequestMethod('post');
        $action01->addParameter(new RequestParameter('name', 'string'));
        $action01->addParameter(new RequestParameter('pass', 'string', true));
        
        //add the action to the API
        //note the 'true' in here. It means the action
        //require authorization.
        $this->addAction($action01,true);
        
        //calling process in the constructor
        $this->process();
    }
    /**
     * Checks if the user is authorized to perform specific action.
     * The method will return true only if the action is equal to 'my-action'.
     */
    public function isAuthorized(){
        $action = $this->getAction();
        if($action == 'auth-action'){
            $i = $this->getInputs();
            $pass = isset($i['pass']) ? $i['pass'] : null;
            if($pass == '123'){
                return true;
            }
        }
        return false;
    }
    /**
     * Process client request based on the given input.
     */
    public function processRequest(){
        $action = $this->getAction();
        $inputs = $this->getInputs();
        if($action == 'my-action'){
            header('content-type:text/plain');
            if(isset($inputs['my-param'])){
                echo '"my-param" = '.$inputs['my-param'];
            }
            else{
                echo '"my-param" is not set';
            }
        }
        else if($action == 'auth-action'){
            header('content-type:text/plain');
            echo 'Dear '.$inputs['name'].', you are authorized to access the API.';
        }
    }
}
//create an instance once a request to the file is made. 
$a = new MyAPI();

/*
 * Assuming that you are testing in 'localhost' and your code is placed 
 * in a folder which has the name 'restEasy/examples', You can perform the 
 * following GET requests to the API:
 * 
 * 1- http://localhost/restEasy/src/examples/basic-usage.php
 * This will output the following JSON:
 * {
 *     "message":"Action is not set.",
 *     "type":"error",
 *     "http-code":404
 * }
 * 
 * 2- http://localhost/restEasy/src/examples/basic-usage.php?action=my-action
 * This will output the following string: ""my-param" is not set".
 * 
 * 3- http://localhost/restEasy/src/examples/basic-usage.php?action=my-action&my-param=hello%20world
 * This will output the following string: ""my-param" = hello world".
 * 
 * 4- http://localhost/restEasy/src/examples/basic-usage.php?action=auth-action
 * This will output the following JSON:
 * {
 *     "message":"The following required parameter(s) where missing from the request body: 'name'.",
 *     "type":"error",
 *     "http-code":404
 * }
 * 
 * 5- http://localhost/restEasy/src/examples/basic-usage.php?action=auth-action&name=Ibrahim
 * This will output the following JSON:
 * {
 *     "message":"Not authorized.",
 *     "type":"error",
 *     "http-code":401
 * }
 * 
 * 6- http://localhost/restEasy/src/examples/basic-usage.php?action=auth-action&name=Ibrahim&pass=1234
 * This will output the following JSON:
 * {
 *     "message":"Not authorized.",
 *     "type":"error",
 *     "http-code":401
 * }
 * 
 * 7- http://localhost/restEasy/src/examples/basic-usage.php?action=auth-action&name=Ibrahim&pass=123
 * This will output the following string: "Dear Ibrahim, you are authorized to access the API."
 */