<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\ParameterSet;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;

class AddressParams implements ParameterSet {
    public function getParameters(): array {
        return [
            'street' => [
                ParamOption::TYPE => ParamType::STRING,
            ],
            'city' => [
                ParamOption::TYPE => ParamType::STRING,
            ],
            'zip' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::PATTERN => '/^[0-9]{5}$/',
            ],
            'country' => [
                ParamOption::TYPE => ParamType::STRING,
                ParamOption::ALLOWED_VALUES => ['US', 'UK', 'DE'],
            ],
        ];
    }
}
