<?php
namespace WebFiori\Tests\Http\TestServices;

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

#[RestController('allow-empty-param')]
class AllowEmptyParamService extends WebService {
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam(name: 'notes', type: ParamType::STRING, optional: true, allowEmpty: true)]
    #[RequestParam(name: 'title', type: ParamType::STRING)]
    public function create(?string $notes, string $title): array {
        return ['title' => $title, 'notes' => $notes ?? ''];
    }
}
