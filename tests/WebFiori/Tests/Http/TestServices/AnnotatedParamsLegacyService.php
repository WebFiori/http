<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

/**
 * A service that uses #[RequestParam] annotations with the traditional
 * processRequest() pattern (no #[ResponseBody]).
 * This is the pattern that was broken before the fix.
 */
#[RestController('annotated-params-legacy')]
class AnnotatedParamsLegacyService extends WebService {
    public function isAuthorized(): bool {
        return true;
    }

    #[PostMapping]
    #[RequestParam(name: 'username', type: ParamType::STRING)]
    #[RequestParam(name: 'password', type: ParamType::STRING)]
    public function processRequest() {
        $username = $this->getParamVal('username');
        $password = $this->getParamVal('password');

        if ($username === null) {
            $this->sendResponse('Username is missing', 400, 'error');
            return;
        }

        if ($password === null) {
            $this->sendResponse('Password is missing', 400, 'error');
            return;
        }

        $this->sendResponse('Login successful for: ' . $username, 200, 'success');
    }
}
