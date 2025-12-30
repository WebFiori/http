<?php
/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2019 WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/http/blob/master/LICENSE
 */
namespace WebFiori\Http;

use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\PutMapping;
use WebFiori\Http\Annotations\DeleteMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Exceptions\HttpException;
use WebFiori\Json\Json;
use WebFiori\Json\JsonI;
/**
 * A class that represents one web service.
 * 
 * A web service is simply an action that is performed by a web 
 * server to do something. For example, It is possible to have a web service 
 * which is responsible for creating new user profile. Think of it as an 
 * action taken to perform specific task.
 * 
 * @author Ibrahim
 * 
 * 
 */
class WebService implements JsonI {
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type error.
     * 
     */
    const E = 'error';
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type info.
     * 
     */
    const I = 'info';
    /**
     * A constant which is used to indicate that the message that will be 
     * sent is of type success.
     * 
     */
    const S = 'success';
    /**
     * The name of the service.
     * 
     * @var string
     * 
     */
    private $name;
    /**
     * The manager that the service belongs to.
     * 
     * @var WebServicesManager
     * 
     */
    private $owner;
    /**
     * An array that holds an objects of type RequestParameter.
     * 
     * @var array
     * 
     */
    private $parameters;
    /**
     * An array that contains service request methods.
     * 
     * @var array
     * 
     */
    private $reqMethods;
    /**
     * The request instance used by the service.
     * 
     * @var Request
     * 
     */
    private $request;
    /**
     * This is used to indicate if authentication is required when the service 
     * is called.
     * 
     * @var bool
     * 
     */
    private $requireAuth;
    /**
     * An array that contains descriptions of 
     * possible responses.
     * 
     * @var array
     * 
     */
    private $responses;
    private array $responsesByMethod = [];
    /**
     * An optional description for the service.
     * 
     * @var string
     * 
     */
    private $serviceDesc;
    /**
     * An attribute that is used to tell since which API version the 
     * service was added.
     * 
     * @var string
     * 
     */
    private $sinceVersion;
    /**
     * Creates new instance of the class.
     * 
     * The developer can supply an optional service name. 
     * A valid service name must follow the following rules:
     * <ul>
     * <li>It can contain the letters [A-Z] and [a-z].</li>
     * <li>It can contain the numbers [0-9].</li>
     * <li>It can have the character '-' and the character '_'.</li>
     * </ul>
     * If The given name is invalid, the name of the service will be set to 'new-service'.
     * 
     * @param string $name The name of the web service. 
     * 
     * @param WebServicesManager|null $owner The manager which is used to
     * manage the web service.
     */
    public function __construct(string $name = '') {
        $this->reqMethods = [];
        $this->parameters = [];
        $this->responses = [];
        $this->responsesByMethod = [];
        $this->requireAuth = true;
        $this->sinceVersion = '1.0.0';
        $this->serviceDesc = '';
        $this->request = Request::createFromGlobals();
        
        $this->configureFromAnnotations($name);
    }
    
    /**
     * Configure service from annotations if present.
     */
    private function configureFromAnnotations(string $fallbackName): void {
        $reflection = new \ReflectionClass($this);
        $attributes = $reflection->getAttributes(\WebFiori\Http\Annotations\RestController::class);
        
        if (!empty($attributes)) {
            $restController = $attributes[0]->newInstance();
            $serviceName = $restController->name ?: $fallbackName;
            $description = $restController->description;
        } else {
            $serviceName = $fallbackName;
            $description = '';
        }
        
        if (!$this->setName($serviceName)) {
            $this->setName('new-service');
        }
        
        if ($description) {
            $this->setDescription($description);
        }
        
        $this->configureMethodMappings();
        $this->configureAuthentication();
    }
    
    /**
     * Process the web service request with auto-processing support.
     * This method should be called instead of processRequest() for auto-processing.
     */
    public function processWithAutoHandling(): void {
        $targetMethod = $this->getTargetMethod();
        
        if ($targetMethod && $this->hasResponseBodyAnnotation($targetMethod)) {
            // Check method-level authorization first
            if (!$this->checkMethodAuthorization()) {
                $this->sendResponse('Access denied', 403, 'error');
                return;
            }
            
            try {
                // Inject parameters into method call
                $params = $this->getMethodParameters($targetMethod);
                $result = $this->$targetMethod(...$params);
                $this->handleMethodResponse($result, $targetMethod);
            } catch (HttpException $e) {
                // Handle HTTP exceptions automatically
                $this->handleException($e);
            } catch (\Exception $e) {
                // Handle other exceptions as 500 Internal Server Error
                $this->sendResponse($e->getMessage(), 500, 'error');
            }
        } else {
            // Fall back to traditional processRequest() approach
            $this->processRequest();
        }
    }
    
    /**
     * Check if a method has the ResponseBody annotation.
     * 
     * @param string $methodName The method name to check
     * @return bool True if the method has ResponseBody annotation
     */
    public function hasResponseBodyAnnotation(string $methodName): bool {
        try {
            $reflection = new \ReflectionMethod($this, $methodName);
            return !empty($reflection->getAttributes(ResponseBody::class));
        } catch (\ReflectionException $e) {
            return false;
        }
    }
    
    /**
     * Handle HTTP exceptions by converting them to appropriate responses.
     * 
     * @param HttpException $exception The HTTP exception to handle
     */
    protected function handleException(HttpException $exception): void {
        $this->sendResponse(
            $exception->getMessage(),
            $exception->getStatusCode(),
            $exception->getResponseType()
        );
    }
    
    /**
     * Configure parameters dynamically for a specific method.
     * 
     * @param string $methodName The method name to configure parameters for
     */
    public function configureParametersForMethod(string $methodName): void {
        try {
            $reflection = new \ReflectionMethod($this, $methodName);
            $this->configureParametersFromMethod($reflection);
        } catch (\ReflectionException $e) {
            // Method doesn't exist, ignore
        }
    }

    /**
     * Configure parameters for all methods with RequestParam annotations.
     */
    private function configureAllAnnotatedParameters(): void {
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getMethods() as $method) {
            $paramAttributes = $method->getAttributes(\WebFiori\Http\Annotations\RequestParam::class);
            if (!empty($paramAttributes)) {
                $this->configureParametersFromMethod($method);
            }
        }
    }
    
    /**
     * Configure parameters for methods with specific HTTP method mapping.
     * 
     * @param string $httpMethod HTTP method (GET, POST, PUT, DELETE, etc.)
     */
    private function configureParametersForHttpMethod(string $httpMethod): void {
        $reflection = new \ReflectionClass($this);
        $httpMethod = strtoupper($httpMethod);
        
        foreach ($reflection->getMethods() as $method) {
            // Check if method has HTTP method mapping annotation
            $mappingFound = false;
            
            // Check for specific HTTP method annotations
            $annotations = [
                'GET' => \WebFiori\Http\Annotations\GetMapping::class,
                'POST' => \WebFiori\Http\Annotations\PostMapping::class,
                'PUT' => \WebFiori\Http\Annotations\PutMapping::class,
                'DELETE' => \WebFiori\Http\Annotations\DeleteMapping::class,
                'PATCH' => \WebFiori\Http\Annotations\PatchMapping::class,
            ];
            
            if (isset($annotations[$httpMethod])) {
                $mappingFound = !empty($method->getAttributes($annotations[$httpMethod]));
            }
            
            if ($mappingFound) {
                $this->configureParametersFromMethod($method);
            }
        }
    }
    
    /**
     * Configure authentication from annotations.
     */
    private function configureAuthentication(): void {
        $reflection = new \ReflectionClass($this);
        
        // Check class-level authentication
        $classAuth = $this->getAuthenticationFromClass($reflection);
        
        // If class has AllowAnonymous, disable auth requirement
        if ($classAuth['allowAnonymous']) {
            $this->setIsAuthRequired(false);
        } else if ($classAuth['requiresAuth'] || $classAuth['preAuthorize']) {
            $this->setIsAuthRequired(true);
        }
    }
    
    /**
     * Get authentication configuration from class annotations.
     */
    private function getAuthenticationFromClass(\ReflectionClass $reflection): array {
        return [
            'allowAnonymous' => !empty($reflection->getAttributes(\WebFiori\Http\Annotations\AllowAnonymous::class)),
            'requiresAuth' => !empty($reflection->getAttributes(\WebFiori\Http\Annotations\RequiresAuth::class)),
            'preAuthorize' => $reflection->getAttributes(\WebFiori\Http\Annotations\PreAuthorize::class)
        ];
    }
    
    /**
     * Check method-level authorization before processing.
     */
    public function checkMethodAuthorization(): bool {
        $reflection = new \ReflectionClass($this);
        $method = $this->getCurrentProcessingMethod() ?: $this->getTargetMethod();
        
        if (!$method) {
            return $this->isAuthorized();
        }
        
        $reflectionMethod = $reflection->getMethod($method);
        
        // Check AllowAnonymous first
        if (!empty($reflectionMethod->getAttributes(\WebFiori\Http\Annotations\AllowAnonymous::class))) {
            return true;
        }
        
        // Check RequiresAuth
        if (!empty($reflectionMethod->getAttributes(\WebFiori\Http\Annotations\RequiresAuth::class))) {
            if (!SecurityContext::isAuthenticated()) {
                return false;
            }
        }
        
        // Check PreAuthorize
        $preAuthAttributes = $reflectionMethod->getAttributes(\WebFiori\Http\Annotations\PreAuthorize::class);
        if (!empty($preAuthAttributes)) {
            $preAuth = $preAuthAttributes[0]->newInstance();

            return SecurityContext::evaluateExpression($preAuth->expression);
        }
        
        return $this->isAuthorized();
    }
    
    /**
     * Check if the method has any authorization annotations.
     */
    public function hasMethodAuthorizationAnnotations(): bool {
        $reflection = new \ReflectionClass($this);
        $method = $this->getCurrentProcessingMethod() ?: $this->getTargetMethod();
        
        if (!$method) {
            return false;
        }
        
        $reflectionMethod = $reflection->getMethod($method);
        
        return !empty($reflectionMethod->getAttributes(\WebFiori\Http\Annotations\AllowAnonymous::class)) ||
               !empty($reflectionMethod->getAttributes(\WebFiori\Http\Annotations\RequiresAuth::class)) ||
               !empty($reflectionMethod->getAttributes(\WebFiori\Http\Annotations\PreAuthorize::class));
    }
    
    /**
     * Get the current processing method name (to be overridden by subclasses if needed).
     */
    protected function getCurrentProcessingMethod(): ?string {
        return null; // Default implementation
    }
    
    /**
     * Get the target method name based on current HTTP request.
     * 
     * @return string|null The method name that should handle this request, or null if none found
     */
    public function getTargetMethod(): ?string {
        $httpMethod = $this->getManager() ? 
            $this->getManager()->getRequest()->getMethod() : 
            ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        // First try to get method from getCurrentProcessingMethod (if implemented)
        $currentMethod = $this->getCurrentProcessingMethod();
        if ($currentMethod) {
            $reflection = new \ReflectionClass($this);
            try {
                $method = $reflection->getMethod($currentMethod);
                if ($this->methodHandlesHttpMethod($method, $httpMethod)) {
                    return $currentMethod;
                }
            } catch (\ReflectionException $e) {
                // Method doesn't exist, continue with discovery
            }
        }
        
        // Fall back to finding first method that matches HTTP method
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->methodHandlesHttpMethod($method, $httpMethod)) {
                return $method->getName();
            }
        }
        
        return null;
    }
    
    /**
     * Check if a method handles the specified HTTP method.
     * 
     * @param \ReflectionMethod $method The method to check
     * @param string $httpMethod The HTTP method (GET, POST, etc.)
     * @return bool True if the method handles this HTTP method
     */
    private function methodHandlesHttpMethod(\ReflectionMethod $method, string $httpMethod): bool {
        $methodMappings = [
            GetMapping::class => RequestMethod::GET,
            PostMapping::class => RequestMethod::POST,
            PutMapping::class => RequestMethod::PUT,
            DeleteMapping::class => RequestMethod::DELETE
        ];
        
        foreach ($methodMappings as $annotationClass => $mappedMethod) {
            if ($httpMethod === $mappedMethod && !empty($method->getAttributes($annotationClass))) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get method parameters by extracting values from request.
     * 
     * @param string $methodName The method name
     * @return array Array of parameter values in correct order
     */
    private function getMethodParameters(string $methodName): array {
        $reflection = new \ReflectionMethod($this, $methodName);
        $params = [];
        
        foreach ($reflection->getParameters() as $param) {
            $paramName = $param->getName();
            $value = $this->getParamVal($paramName);
            
            // Handle optional parameters with defaults
            if ($value === null && $param->isDefaultValueAvailable()) {
                $value = $param->getDefaultValue();
            }
            
            $params[] = $value;
        }
        
        return $params;
    }
    
    /**
     * Handle method response by auto-converting return values to HTTP responses.
     * 
     * @param mixed $result The return value from the method
     * @param string $methodName The name of the method that was called
     * @return void
     */
    protected function handleMethodResponse(mixed $result, string $methodName): void {
        $reflection = new \ReflectionMethod($this, $methodName);
        $responseBodyAttrs = $reflection->getAttributes(ResponseBody::class);
        
        if (empty($responseBodyAttrs)) {
            return; // No auto-processing, method should handle response manually
        }
        
        $responseBody = $responseBodyAttrs[0]->newInstance();
        
        // Auto-convert return value to response
        if ($result === null) {
            // Null return = empty response with configured status
            $this->sendResponse('', $responseBody->status, $responseBody->type);
        } elseif (is_array($result) || is_object($result)) {
            // Array/object = JSON response
            $this->sendResponse('Success', $responseBody->status, $responseBody->type, $result);
        } else {
            // String/scalar = plain response
            $this->sendResponse($result, $responseBody->status, $responseBody->type);
        }
    }
    
    /**
     * Configure allowed HTTP methods from method annotations.
     */
    private function configureMethodMappings(): void {
        $reflection = new \ReflectionClass($this);
        $httpMethodToMethods = [];
        
        foreach ($reflection->getMethods() as $method) {
            $methodMappings = [
                GetMapping::class => RequestMethod::GET,
                PostMapping::class => RequestMethod::POST,
                PutMapping::class => RequestMethod::PUT,
                DeleteMapping::class => RequestMethod::DELETE
            ];
            
            foreach ($methodMappings as $annotationClass => $httpMethod) {
                $attributes = $method->getAttributes($annotationClass);
                if (!empty($attributes)) {
                    if (!isset($httpMethodToMethods[$httpMethod])) {
                        $httpMethodToMethods[$httpMethod] = [];
                    }
                    $httpMethodToMethods[$httpMethod][] = $method->getName();
                }
            }
        }
        
        // Check for duplicates only if getCurrentProcessingMethod is not overridden
        $hasCustomRouting = $reflection->getMethod('getCurrentProcessingMethod')->getDeclaringClass()->getName() !== self::class;
        
        if (!$hasCustomRouting) {
            foreach ($httpMethodToMethods as $httpMethod => $methods) {
                if (count($methods) > 1) {
                    throw new Exceptions\DuplicateMappingException(
                        "HTTP method $httpMethod is mapped to multiple methods: " . implode(', ', $methods)
                    );
                }
            }
        }
        
        if (!empty($httpMethodToMethods)) {
            $this->setRequestMethods(array_keys($httpMethodToMethods));
        }
    }
    
    /**
     * Configure parameters from method RequestParam annotations.
     */
    private function configureParametersFromMethod(\ReflectionMethod $method): void {
        $paramAttributes = $method->getAttributes(\WebFiori\Http\Annotations\RequestParam::class);
        
        foreach ($paramAttributes as $attribute) {
            $param = $attribute->newInstance();
            
            $options = [
                \WebFiori\Http\ParamOption::TYPE => $this->mapParamType($param->type),
                \WebFiori\Http\ParamOption::OPTIONAL => $param->optional,
                \WebFiori\Http\ParamOption::DEFAULT => $param->default,
                \WebFiori\Http\ParamOption::DESCRIPTION => $param->description
            ];
            
            if ($param->filter !== null) {
                $options[\WebFiori\Http\ParamOption::FILTER] = $param->filter;
            }
            
            $this->addParameters([
                $param->name => $options
            ]);
        }
    }
    
    /**
     * Map string type to ParamType constant.
     */
    private function mapParamType(string $type): string {
        return match(strtolower($type)) {
            'int', 'integer' => \WebFiori\Http\ParamType::INT,
            'float', 'double' => \WebFiori\Http\ParamType::DOUBLE,
            'bool', 'boolean' => \WebFiori\Http\ParamType::BOOL,
            'email' => \WebFiori\Http\ParamType::EMAIL,
            'url' => \WebFiori\Http\ParamType::URL,
            'array' => \WebFiori\Http\ParamType::ARR,
            'json' => \WebFiori\Http\ParamType::JSON_OBJ,
            default => \WebFiori\Http\ParamType::STRING
        };
    }    /**
     * Returns an array that contains all possible requests methods at which the 
     * service can be called with.
     * 
     * The array will contain strings like 'GET' or 'POST'. If no request methods
     * where added, the array will be empty.
     * 
     * @return array An array that contains all possible requests methods at which the 
     * service can be called using.
     * 
     */
    public function &getRequestMethods() : array {
        return $this->reqMethods;
    }
    /**
     * Returns an array that contains an objects of type RequestParameter.
     * 
     * @return array an array that contains an objects of type RequestParameter.
     * 
     */
    public final function &getParameters() : array {
        return $this->parameters;
    }
    /**
     * 
     * @return string
     * 
     */
    public function __toString() {
        return $this->toJSON().'';
    }
    /**
     * Adds new request parameter to the service.
     * 
     * The parameter will only be added if no parameter which has the same 
     * name as the given one is added before.
     * 
     * @param RequestParameter|array $param The parameter that will be added. It 
     * can be an object of type 'RequestParameter' or an associative array of 
     * options. The array can have the following indices:
     * <ul>
     * <li><b>name</b>: The name of the parameter. It must be provided.</li>
     * <li><b>type</b>: The datatype of the parameter. If not provided, 'string' is used.</li>
     * <li><b>optional</b>: A boolean. If set to true, it means the parameter is 
     * optional. If not provided, 'false' is used.</li>
     * <li><b>min</b>: Minimum value of the parameter. Applicable only for 
     * numeric types.</li>
     * <li><b>max</b>: Maximum value of the parameter. Applicable only for 
     * numeric types.</li>
     * <li><b>allow-empty</b>: A boolean. If the type of the parameter is string or string-like 
     * type and this is set to true, then empty strings will be allowed. If 
     * not provided, 'false' is used.</li>
     * <li><b>custom-filter</b>: A PHP function that can be used to filter the 
     * parameter even further</li>
     * <li><b>default</b>: An optional default value to use if the parameter is 
     * not provided and is optional.</li>
     * <li><b>description</b>: The description of the attribute.</li>
     * </ul>
     * 
     * @return bool If the given request parameter is added, the method will 
     * return true. If it was not added for any reason, the method will return 
     * false.
     * 
     */
    public function addParameter($param) : bool {
        if (gettype($param) == 'array') {
            $param = RequestParameter::create($param);
        }

        if ($param instanceof RequestParameter && !$this->hasParameter($param->getName())) {
            // Additional validation for reserved parameter names
            if (in_array(strtolower($param->getName()), \WebFiori\Http\RequestParameter::RESERVED_NAMES)) {
                throw new \InvalidArgumentException("Cannot add parameter '" . $param->getName() . "' to service '" . $this->getName() . "': parameter name is reserved. Reserved names: " . implode(', ', \WebFiori\Http\RequestParameter::RESERVED_NAMES));
            }

            $this->parameters[] = $param;

            return true;
        }

        return false;
    }
    /**
     * Adds multiple parameters to the web service in one batch.
     * 
     * @param array $params An associative or indexed array. If the array is indexed, 
     * each index should hold an object of type 'RequestParameter'. If it is associative,
     * then the key will represent the name of the web service and the value of the 
     * key should be a sub-associative array that holds parameter options.
     * 
     */
    public function addParameters(array $params) {
        foreach ($params as $paramIndex => $param) {
            if ($param instanceof RequestParameter) {
                $this->addParameter($param);
            } else if (gettype($param) == 'array') {
                $param['name'] = $paramIndex;
                $this->addParameter(RequestParameter::create($param));
            }
        }
    }
    /**
     * Adds new request method.
     * 
     * The value that will be passed to this method can be any string 
     * that represents HTTP request method (e.g. 'get', 'post', 'options' ...). It 
     * can be in upper case or lower case.
     * 
     * @param string $method The request method.
     * 
     * @return bool true in case the request method is added. If the given 
     * request method is already added or the method is unknown, the method 
     * will return false.
     * 
     */
    public final function addRequestMethod(string $method) : bool {
        $uMethod = strtoupper(trim($method));

        if (in_array($uMethod, RequestMethod::getAll()) && !in_array($uMethod, $this->reqMethods)) {
            $this->reqMethods[] = $uMethod;

            return true;
        }

        return false;
    }
    /**
     * Adds response description.
     * 
     * It is used to describe the API for front-end developers and help them 
     * identify possible responses if they call the API using the specified service.
     * 
     * @param string $description A paragraph that describes one of 
     * the possible responses due to calling the service.
     */
    public function addResponse(string $method, string $statusCode, OpenAPI\ResponseObj|string $response): WebService {
        $method = strtoupper($method);
        
        if (!isset($this->responsesByMethod[$method])) {
            $this->responsesByMethod[$method] = new OpenAPI\ResponsesObj();
        }
        
        $this->responsesByMethod[$method]->addResponse($statusCode, $response);
        return $this;
    }

    public final function addResponseDescription(string $description) {
        $trimmed = trim($description);

        if (strlen($trimmed) != 0) {
            $this->responses[] = $trimmed;
        }
    }
    public function getResponsesForMethod(string $method): ?OpenAPI\ResponsesObj {
        $method = strtoupper($method);
        return $this->responsesByMethod[$method] ?? null;
    }
    /**
     * Sets all responses for a specific HTTP method.
     * 
     * @param string $method HTTP method.
     * @param OpenAPI\ResponsesObj $responses Responses object.
     * 
     * @return WebService Returns self for method chaining.
     */
    public function setResponsesForMethod(string $method, OpenAPI\ResponsesObj $responses): WebService {
        $this->responsesByMethod[strtoupper($method)] = $responses;
        return $this;
    }
    
    /**
     * Gets all responses mapped by HTTP method.
     * 
     * @return array<string, OpenAPI\ResponsesObj> Map of methods to responses.
     */
    public function getAllResponses(): array {
        return $this->responsesByMethod;
    }
    
    /**
     * Converts this web service to an OpenAPI PathItemObj.
     * 
     * Each HTTP method supported by this service becomes an operation in the path item.
     * 
     * @return OpenAPI\PathItemObj The PathItemObj representation of this service.
     */
    public function toPathItemObj(): OpenAPI\PathItemObj {
        $pathItem = new OpenAPI\PathItemObj();
        
        foreach ($this->getRequestMethods() as $method) {
            $responses = $this->getResponsesForMethod($method);
            
            if ($responses === null) {
                $responses = new OpenAPI\ResponsesObj();
                $responses->addResponse('200', 'Successful operation');
            }
            
            $operation = new OpenAPI\OperationObj($responses);
            
            switch ($method) {
                case RequestMethod::GET:
                    $pathItem->setGet($operation);
                    break;
                case RequestMethod::POST:
                    $pathItem->setPost($operation);
                    break;
                case RequestMethod::PUT:
                    $pathItem->setPut($operation);
                    break;
                case RequestMethod::DELETE:
                    $pathItem->setDelete($operation);
                    break;
                case RequestMethod::PATCH:
                    $pathItem->setPatch($operation);
                    break;
        }
        
        
    }return $pathItem;}
    /**
     * Returns an object that contains the value of the header 'authorization'.
     * 
     * @return AuthHeader|null The object will have two primary attributes, the first is 
     * the 'scheme' and the second one is 'credentials'. The 'scheme' 
     * will contain the name of the scheme which is used to authenticate 
     * ('basic', 'bearer', 'digest', etc...). The 'credentials' will contain 
     * the credentials which can be used to authenticate the client.
     * 
     */
    public function getAuthHeader() {
        if ($this->request !== null) {
            return $this->request->getAuthHeader();
        }
        return null;
    }

    /**
     * Sets the request instance for the service.
     * 
     * @param mixed $request The request instance (Request, etc.)
     */
    public function setRequest($request) {
        $this->request = $request;
    }
    /**
     * Returns the description of the service.
     * 
     * @return string The description of the service. Default is empty string.
     * 
     */
    public final function getDescription() : string {
        return $this->serviceDesc;
    }
    /**
     * Returns an associative array or an object of type Json of filtered request inputs.
     * 
     * The indices of the array will represent request parameters and the 
     * values of each index will represent the value which was set in 
     * request body. The values will be filtered and might not be exactly the same as 
     * the values passed in request body. Note that if a parameter is optional and not 
     * provided in request body, its value will be set to 'null'. Note that 
     * if request content type is 'application/json', only basic filtering will 
     * be applied. Also, parameters in this case don't apply.
     * 
     * @return array|Json|null An array of filtered request inputs. This also can 
     * be an object of type 'Json' if request content type was 'application/json'. 
     * If no manager was associated with the service, the method will return null.
     * 
     */
    public function getInputs() {
        $manager = $this->getManager();

        if ($manager !== null) {
            return $manager->getInputs();
        }

        return null;
    }
    /**
     * Returns the manager which is used to manage the web service.
     * 
     * @return WebServicesManager|null If set, it is returned as an object.
     * Other than that, null is returned.
     */
    public function getManager() {
        return $this->owner;
    }
    /**
     * Returns the name of the service.
     * 
     * @return string The name of the service.
     * 
     */
    public final function getName() : string {
        return $this->name;
    }
    /**
     * Map service parameter to specific instance of a class.
     * 
     * This method assumes that every parameter in the request has a method
     * that can be called to set attribute value. For example, if a parameter 
     * has the name 'user-last-name', the mapping method should have the name
     * 'setUserLastName' for mapping to work correctly.
     * 
     * @param string $clazz The class that service parameters will be mapped
     * to.
     * 
     * @param array $settersMap An optional array that can have custom
     * setters map. The indices of the array should be parameters names
     * and the values are the names of setter methods in the class.
     * 
     * @return object The Method will return an instance of the class with
     * all its attributes set to request parameter's values.
     */
    public function getObject(string $clazz, array $settersMap = []) {
        $mapper = new ObjectMapper($clazz, $this);

        foreach ($settersMap as $param => $method) {
            $mapper->addSetterMap($param, $method);
        }

        return $mapper->map($this->getInputs());
    }
    /**
     * Returns one of the parameters of the service given its name.
     * 
     * @param string $paramName The name of the parameter.
     * 
     * @return RequestParameter|null Returns an objects of type RequestParameter if 
     * a parameter with the given name was found. null if nothing is found.
     * 
     */
    public final function getParameterByName(string $paramName, ?string $httpMethod = null) {
        // Configure parameters if HTTP method specified
        if ($httpMethod !== null) {
            $this->configureParametersForHttpMethod($httpMethod);
        } else {
            // Configure parameters for all methods with annotations
            $this->configureAllAnnotatedParameters();
        }
        
        $trimmed = trim($paramName);

        if (strlen($trimmed) != 0) {
            foreach ($this->parameters as $param) {
                if ($param->getName() == $trimmed) {
                    return $param;
                }
            }
        }

        return null;
    }
    /**
     * Returns the value of request parameter given its name.
     * 
     * @param string $paramName The name of request parameter as specified when 
     * it was added to the service.
     * 
     * @return mixed|null If the parameter is found and its value is set, the 
     * method will return its value. Other than that, the method will return null. 
     * For optional parameters, if a default value is set for it, the method will
     * return that value.
     * 
     */
    public function getParamVal(string $paramName) {
        $inputs = $this->getInputs();
        $trimmed = trim($paramName);

        if ($inputs !== null) {
            if ($inputs instanceof Json) {
                return $inputs->get($trimmed);
            } else {
                return $inputs[$trimmed] ?? null;
            }
        }

        return null;
    }
    /**
     * Returns an indexed array that contains information about possible responses.
     * 
     * It is used to describe the API for front-end developers and help them 
     * identify possible responses if they call the API using the specified service.
     * 
     * @return array An array that contains information about possible responses.
     * 
     */
    public final function getResponsesDescriptions() : array {
        return $this->responses;
    }
    /**
     * Returns version number or name at which the service was added to the API.
     * 
     * Version number is set based on the version number which was set in the 
     * class WebAPI.
     * 
     * @return string The version number at which the service was added to the API. 
     * Default is '1.0.0'.
     * 
     */
    public final function getSince() : string {
        return $this->sinceVersion;
    }
    /**
     * Checks if the service has a specific request parameter given its name.
     * 
     * Note that the name of the parameter is case-sensitive. This means that
     * 'get-profile' is not the same as 'Get-Profile'.
     * 
     * @param string $name The name of the parameter.
     * 
     * @return bool If a request parameter which has the given name is added 
     * to the service, the method will return true. Otherwise, the method will return 
     * false.
     * 
     */
    public function hasParameter(string $name) : bool {
        $trimmed = trim($name);

        if (strlen($name) != 0) {
            foreach ($this->getParameters() as $param) {
                if ($param->getName() == $trimmed) {
                    return true;
                }
            }
        }

        return false;
    }
    /**
     * Checks if the client is authorized to use the service or not.
     * 
     * The developer should implement this method in a way it returns a boolean. 
     * If the method returns true, it means the client is allowed to use the service. 
     * If the method returns false, then he is not authorized and a 401 error 
     * code will be sent back. If the method returned nothing, then it means the 
     * user is authorized to call the API. If WebFiori framework is used, it is 
     * possible to perform the functionality of this method using middleware.
     * 
     * @return bool True if the user is allowed to perform the action. False otherwise.
     * 
     */
    public function isAuthorized() : bool {return false;}
    /**
     * Returns the value of the property 'requireAuth'.
     * 
     * The property is used to tell if the authorization step will be skipped 
     * or not when the service is called. 
     * 
     * @return bool The method will return true if authorization step required. 
     * False if the authorization step will be skipped. Default return value is true.
     * 
     */
    public function isAuthRequired() : bool {
        return $this->requireAuth;
    }

    /**
     * Validates the name of a web service or request parameter.
     *
     * @param string $name The name of the service or parameter.
     *
     * @return bool If valid, true is returned. Other than that, false is returned.
     */
    public static function isValidName(string $name): bool {
        $trimmedName = trim($name);
        $len = strlen($trimmedName);

        if ($len != 0) {
            for ($x = 0 ; $x < $len ; $x++) {
                $ch = $trimmedName[$x];

                if (!($ch == '_' || $ch == '-' || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z') || ($ch >= '0' && $ch <= '9'))) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
    /**
     * Process client's request.
     */
    public function processRequest() {}
    /**
     * Removes a request parameter from the service given its name.
     * 
     * @param string $paramName The name of the parameter (case-sensitive).
     * 
     * @return null|RequestParameter If a parameter which has the given name 
     * was removed, the method will return an object of type 'RequestParameter' 
     * that represents the removed parameter. If nothing is removed, the 
     * method will return null.
     * 
     */
    public function removeParameter(string $paramName) {
        $trimmed = trim($paramName);
        $params = &$this->getParameters();
        $index = -1;
        $count = count($params);

        for ($x = 0 ; $x < $count ; $x++) {
            if ($params[$x]->getName() == $trimmed) {
                $index = $x;
                break;
            }
        }
        $retVal = null;

        if ($index != -1) {
            if ($count == 1) {
                $retVal = $params[0];
                unset($params[0]);
            } else {
                $retVal = $params[$index];
                $params[$index] = $params[$count - 1];
                unset($params[$count - 1]);
            }
        }

        return $retVal;
    }
    /**
     * Removes a request method from the previously added ones. 
     * 
     * @param string $method The request method (e.g. 'get', 'post', 'options' ...). It 
     * can be in upper case or lower case.
     * 
     * @return bool If the given request method is remove, the method will 
     * return true. Other than that, the method will return true.
     * 
     */
    public function removeRequestMethod(string $method): bool {
        $uMethod = strtoupper(trim($method));
        $allowedMethods = &$this->getRequestMethods();

        if (in_array($uMethod, $allowedMethods)) {
            $count = count($allowedMethods);
            $methodIndex = -1;

            for ($x = 0 ; $x < $count ; $x++) {
                if ($this->getRequestMethods()[$x] == $uMethod) {
                    $methodIndex = $x;
                    break;
                }
            }

            if ($count == 1) {
                unset($allowedMethods[0]);
            } else {
                $allowedMethods[$methodIndex] = $allowedMethods[$count - 1];
                unset($allowedMethods[$count - 1]);
            }

            return true;
        }

        return false;
    }
    /**
     * Sends Back a data using specific content type and specific response code.
     * 
     * @param string $contentType Response content type (such as 'application/json')
     * 
     * @param mixed $data Any data to send back. Mostly, it will be a string.
     * 
     * @param int $code HTTP response code that will be used to send the data. 
     * Default is HTTP code 200 - Ok.
     * 
     */
    public function send(string $contentType, $data, int $code = 200) {
        $manager = $this->getManager();

        if ($manager !== null) {
            $manager->send($contentType, $data, $code);
        }
    }
    /**
     * Sends a JSON response to the client.
     * 
     * The basic format of the message will be as follows:
     * <p>
     * {<br/>
     * &nbsp;&nbsp;"message":"Action is not set.",<br/>
     * &nbsp;&nbsp;"type":"error"<br/>
     * &nbsp;&nbsp;"http-code":404<br/>
     * &nbsp;&nbsp;"more-info":EXTRA_INFO<br/>
     * }
     * </p>
     * Where EXTRA_INFO can be a simple string or any JSON data.
     * 
     * @param string $message The message to send back.
     * 
     * @param string $type A string that tells the client what is the type of 
     * the message. The developer can specify his own message types such as 
     * 'debug', 'info' or any string. If it is empty string, it will be not 
     * included in response payload.
     * 
     * @param int $code Response code (such as 404 or 200). Default is 200.
     * 
     * @param mixed $otherInfo Any other data to send back it can be a simple 
     * string, an object... . If null is given, the parameter 'more-info' 
     * will be not included in response. Default is empty string. Default is null.
     * 
     */
    public function sendResponse(string $message, int $code = 200, string $type = '', mixed $otherInfo = '') {
        $manager = $this->getManager();

        if ($manager !== null) {
            $manager->sendResponse($message, $code, $type, $otherInfo);
        }
    }
    /**
     * Sets the description of the service.
     * 
     * Used to help front-end to identify the use of the service.
     * 
     * @param string $desc Action description.
     * 
     */
    public final function setDescription(string $desc) {
        $this->serviceDesc = trim($desc);
    }
    /**
     * Sets the value of the property 'requireAuth'.
     * 
     * The property is used to tell if the authorization step will be skipped 
     * or not when the service is called. 
     * 
     * @param bool $bool True to make authorization step required. False to 
     * skip the authorization step.
     * 
     */
    public function setIsAuthRequired(bool $bool) {
        $this->requireAuth = $bool;
    }
    /**
     * Associate the web service with a manager.
     * 
     * The developer does not have to use this method. It is used when a 
     * service is added to a manager.
     * 
     * @param WebServicesManager|null $manager The manager at which the service 
     * will be associated with. If null is given, the association will be removed if 
     * the service was associated with a manager.
     * 
     */
    public function setManager(?WebServicesManager $manager) {
        if ($manager === null) {
            $this->owner = null;
        } else {
            $this->owner = $manager;
        }
    }
    /**
     * Sets the name of the service.
     * 
     * A valid service name must follow the following rules:
     * <ul>
     * <li>It can contain the letters [A-Z] and [a-z].</li>
     * <li>It can contain the numbers [0-9].</li>
     * <li>It can have the character '-' and the character '_'.</li>
     * </ul>
     * 
     * @param string $name The name of the web service.
     * 
     * @return bool If the given name is valid, the method will return 
     * true once the name is set. false is returned if the given 
     * name is invalid.
     * 
     */
    public final function setName(string $name) : bool {
        if (self::isValidName($name)) {
            $this->name = trim($name);

            return true;
        }

        return false;
    }
    /**
     * Adds multiple request methods as one group.
     * 
     * @param array $methods
     */
    public function setRequestMethods(array $methods) {
        foreach ($methods as $m) {
            $this->addRequestMethod($m);
        }
    }
    /**
     * Sets version number or name at which the service was added to a manager.
     * 
     * This method is called automatically when the service is added to any services manager.
     * The developer does not have to use this method.
     * 
     * @param string $sinceAPIv The version number at which the service was added to the API.
     * 
     */
    public final function setSince(string $sinceAPIv) {
        $this->sinceVersion = $sinceAPIv;
    }
    /**
     * Returns a Json object that represents the service.
     * 
     * @return Json an object of type Json.
     * 
     */
    public function toJSON() : Json {
        return $this->toPathItemObj()->toJSON();
    }
}
