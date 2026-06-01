<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\Validate;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

#[RestController('validation-hook-service')]
class ValidationHookService extends WebService {

    /**
     * Service-wide validation: applies to all methods.
     */
    public function validate(array $inputs): array {
        $errors = [];

        if (isset($inputs['email']) && str_contains($inputs['email'], '+')) {
            $errors['email'] = 'Plus-addressing not allowed.';
        }

        return $errors;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[Validate('validateRegistration')]
    #[RequestParam('email', ParamType::EMAIL)]
    #[RequestParam('password', ParamType::STRING)]
    #[RequestParam('password_confirm', ParamType::STRING)]
    public function register(string $email, string $password, string $passwordConfirm): array {
        return ['registered' => true, 'email' => $email];
    }

    /**
     * Method-specific validator for register().
     */
    private function validateRegistration(array $inputs): array {
        $errors = [];

        if ($inputs['password'] !== $inputs['password_confirm']) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }

        if (strlen($inputs['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        return $errors;
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
