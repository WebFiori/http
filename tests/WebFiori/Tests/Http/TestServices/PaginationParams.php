<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\ParameterSet;
use WebFiori\Http\ParamOption;
use WebFiori\Http\ParamType;

class PaginationParams implements ParameterSet {
    public function getParameters(): array {
        return [
            'page' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true,
                ParamOption::DEFAULT => 1,
                ParamOption::MIN => 1
            ],
            'per_page' => [
                ParamOption::TYPE => ParamType::INT,
                ParamOption::OPTIONAL => true,
                ParamOption::DEFAULT => 20,
                ParamOption::MIN => 1,
                ParamOption::MAX => 100
            ],
        ];
    }
}
