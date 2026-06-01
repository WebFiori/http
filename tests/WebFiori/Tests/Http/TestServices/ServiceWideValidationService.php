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

/**
 * Service with only service-wide validate() — no #[Validate] attribute.
 */
#[RestController('service-wide-validation')]
class ServiceWideValidationService extends WebService {

    public function validate(array $inputs): array {
        $errors = [];

        if (isset($inputs['start']) && isset($inputs['end']) && $inputs['end'] <= $inputs['start']) {
            $errors['end'] = 'End must be after start.';
        }

        return $errors;
    }

    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('start', ParamType::INT)]
    #[RequestParam('end', ParamType::INT)]
    public function createRange(int $start, int $end): array {
        return ['start' => $start, 'end' => $end];
    }

    public function isAuthorized(): bool {
        return true;
    }

    public function processRequest() {
    }
}
