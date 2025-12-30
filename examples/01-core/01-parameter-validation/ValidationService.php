<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\GetMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\APIFilter;
use WebFiori\Http\WebService;

/**
 * Service demonstrating comprehensive parameter validation
 */
#[RestController('validate', 'Parameter validation demonstration')]
class ValidationService extends WebService {
    #[GetMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('name', 'string', false, null, 'User name (2-50 characters)')]
    #[RequestParam('age', 'int', true, 25, 'User age (18-120 years)')]
    #[RequestParam('email', 'email', false, null, 'Valid email address')]
    #[RequestParam('website', 'url', true, null, 'Personal website URL')]
    #[RequestParam('score', 'double', true, 0.0, 'Score percentage (0.0-100.0)')]
    #[RequestParam('username', 'string', true, null, 'Username (alphanumeric, 3-20 chars)', filter: [ValidationService::class, 'validateUsername'])]
    public function validateData(string $name, ?int $age = 25, string $email, ?string $website = null, ?float $score = 0.0, ?string $username = null): array {
        return [
            'validated_data' => [
                'name' => $name,
                'age' => $age,
                'email' => $email,
                'website' => $website,
                'score' => $score,
                'username' => $username
            ],
            'validation_info' => [
                'name_length' => strlen($name),
                'age_valid' => $age >= 18 && $age <= 120,
                'email_domain' => substr(strrchr($email, '@'), 1),
                'website_protocol' => $website ? parse_url($website, PHP_URL_SCHEME) : null,
                'username_valid' => $username ? ctype_alnum($username) : null
            ]
        ];
    }

    /**
     * Custom validation function for username
     */
    public static function validateUsername($original, $filtered, $param) {
        // Must be alphanumeric and 3-20 characters
        if (strlen($filtered) < 3 || strlen($filtered) > 20) {
            return APIFilter::INVALID;
        }

        if (!ctype_alnum($filtered)) {
            return APIFilter::INVALID;
        }

        return strtolower($filtered); // Normalize to lowercase
    }
}
