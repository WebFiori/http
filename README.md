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

An end point is represented as the class <a href="https://programmingacademia.com/webfiori/docs/restEasy/APIAction">APIAction</a> and a web service is represented by the class <a href="https://programmingacademia.com/webfiori/docs/restEasy/WebAPI">WebAPI</a>. Also, body parameters represented by the class <a href="https://programmingacademia.com/webfiori/docs/restEasy/RequestParameter">RequestParameter</a>.

## Features
* Full support for creating REST services using JSON notation.
* Support for basic data filtering and validation.
* The ability to create custom filters based on the need.

## Supported PHP Versions
The library support all versions starting from version 5.6 up to version 7.3.

## Installation
If you are using composer to collect your dependencies, you can simply include the following entry in your 'composer.json' file:

``` json
{
    "require": {
        "webfiori/rest-easy":"1.4.5"
    }
}
```
Note that the <a href="https://github.com/usernane/jsonx">JsonX</a> library will be included with the installation files as this library is depending on it. 

Another option is to download the latest release manually from <a href="https://github.com/usernane/restEasy/releases">Release</a>. The latest stable release of the laibrary is <a href="https://github.com/usernane/restEasy/releases/tag/v1.4.5">v1.4.5</a>

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

```
