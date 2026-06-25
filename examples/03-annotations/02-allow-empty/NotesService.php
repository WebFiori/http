<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\Annotations\AllowAnonymous;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\ParamType;
use WebFiori\Http\WebService;

/**
 * Demonstrates using allowEmpty in #[RequestParam] to accept empty strings.
 *
 * Without allowEmpty: sending notes="" results in a 422 validation error.
 * With allowEmpty: true, empty strings are accepted as valid input.
 */
#[RestController('notes', 'Notes service demonstrating allowEmpty')]
#[AllowAnonymous]
class NotesService extends WebService {

    #[PostMapping]
    #[ResponseBody]
    #[RequestParam(name: 'title', type: ParamType::STRING)]
    #[RequestParam(name: 'notes', type: ParamType::STRING, optional: true, allowEmpty: true)]
    public function createNote(string $title, ?string $notes): array {
        return [
            'title' => $title,
            'notes' => $notes ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
