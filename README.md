# RESTEasy
A simple library for creating RESTful web APIs. 
It includes inputs feltering and data validation in addion to creating user-defined inputs filters.

<p align="center">
  <a href="https://travis-ci.org/usernane/restEasy" target="_blank"><img src="https://travis-ci.org/usernane/restEasy.svg?branch=master"></a>
  <a href="https://codecov.io/gh/usernane/restEasy" target="_blank">
    <img src="https://codecov.io/gh/usernane/restEasy/branch/master/graph/badge.svg" />
  </a>
  <a href="https://paypal.me/IbrahimBinAlshikh" target="_blank">
    <img src="https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fprogrammingacademia.com%2Fwebfiori%2Fapis%2Fshields-get-dontate-badget">
  </a>
</p>
## API Docs
This library is a part of <a>WebFiori Framework</a>. To access API docs of the library, you can visid the following link: https://programmingacademia.com/webfiori/docs/restEasy .

## The Idea
The idea of the library is as follows, when a client performs a request to a web service, he is usually intersted in performing specific action. One web service can have multiple actions. An action can be considered as API end point. The client can pass arguments (or parameters) to the end point in request body or as a query string.

An end point is represented as the class <a href="https://programmingacademia.com/webfiori/docs/restEasy/APIAction">APIAction</a> and a web service is represented by the class <a href="https://programmingacademia.com/webfiori/docs/restEasy/WebAPI">WebAPI</a>. Also, body parameters represented by the class <a href="https://programmingacademia.com/webfiori/docs/restEasy/APIAction">APIAction">RequestParameter</a>.

## Usage
The first step is to include the requierd classes. There are basically 3 classes that you need:
* <a href="https://programmingacademia.com/webfiori/docs/restEasy/RequestParameter">RequestParameter</a>
* <a href="https://programmingacademia.com/webfiori/docs/restEasy/APIAction">APIAction</a>
* <a href="https://programmingacademia.com/webfiori/docs/restEasy/WebAPI">WebAPI</a>

After that, you need to extend the class 'WebAPI' and implement two methods:
* <a href="https://programmingacademia.com/webfiori/docs#isAuthorized">WebAPI::isAuthorized()</a>
* <a href="https://programmingacademia.com/webfiori/docs#processRequest">WebAPI::processRequest()</a>

The implementation of the first method will determine if the cliend who is calling the API is authorized to call it. It only applyies to the actions which require permissions. The method must be implemented in a way that it makes it return true if the user is authorized to access a specific end point.

The second method is used to process the call depending on action type (end point).

The following code sample shows how to create a simple web API.

```php
require_once '../API.php';
require_once '../APIAction.php';
require_once '../APIFilter.php';
require_once '../RequestParameter.php';
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
        //add actions, parameters for 'GET' or 'POST'
        
        //create new action
        $action = new APIAction();
        $action->setName('my-action');
        $action->addRequestMethod('get',TRUE);
        
        //add parameters for the action
        $action->addParameter(new RequestParameter('my-param', 'string', TRUE));
        
        //add the action to the API
        $this->addAction($action);
        
        //calling process in the constructor
        $this->process();
    }
    
    public function isAuthorized(){
        return TRUE;
    }
    
    public function processRequest(){
        header('content-type:text/plain');
        $inputs = $this->getInputs();
        if(isset($inputs['my-param'])){
            echo '"my-param" = '.$inputs['my-param'];
        }
        else{
            echo '"my-param" is not set';
        }
    }
}
//create an instance once the file is called. 
$a = new MyAPI();

```
