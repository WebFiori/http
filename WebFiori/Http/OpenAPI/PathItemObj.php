<?php

/**
 * This file is licensed under MIT License.
 * 
 * Copyright (c) 2025-present WebFiori Framework
 * 
 * For more information on the license, please visit: 
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 * 
 */
namespace WebFiori\Http\OpenAPI;

use WebFiori\Json\Json;
use WebFiori\Json\JsonI;

/**
 * Represents a Path Item Object in OpenAPI specification.
 * 
 * Describes the operations available on a single path.
 * A Path Item MAY be empty, due to ACL constraints.
 * 
 * This object MAY be extended with Specification Extensions.
 * 
 * @see https://spec.openapis.org/oas/v3.1.0#path-item-object
 */
class PathItemObj implements JsonI {
    /**
     * A definition of a DELETE operation on this path.
     * 
     * @var OperationObj|null
     */
    private ?OperationObj $delete = null;
    /**
     * A definition of a GET operation on this path.
     * 
     * @var OperationObj|null
     */
    private ?OperationObj $get = null;

    /**
     * A definition of a PATCH operation on this path.
     * 
     * @var OperationObj|null
     */
    private ?OperationObj $patch = null;

    /**
     * A definition of a POST operation on this path.
     * 
     * @var OperationObj|null
     */
    private ?OperationObj $post = null;

    /**
     * A definition of a PUT operation on this path.
     * 
     * @var OperationObj|null
     */
    private ?OperationObj $put = null;

    public function getDelete(): ?OperationObj {
        return $this->delete;
    }

    public function getGet(): ?OperationObj {
        return $this->get;
    }

    public function getPatch(): ?OperationObj {
        return $this->patch;
    }

    public function getPost(): ?OperationObj {
        return $this->post;
    }

    public function getPut(): ?OperationObj {
        return $this->put;
    }

    public function setDelete(OperationObj $operation): PathItemObj {
        $this->delete = $operation;

        return $this;
    }

    public function setGet(OperationObj $operation): PathItemObj {
        $this->get = $operation;

        return $this;
    }

    public function setPatch(OperationObj $operation): PathItemObj {
        $this->patch = $operation;

        return $this;
    }

    public function setPost(OperationObj $operation): PathItemObj {
        $this->post = $operation;

        return $this;
    }

    public function setPut(OperationObj $operation): PathItemObj {
        $this->put = $operation;

        return $this;
    }

    public function toJSON(): Json {
        $json = new Json();

        if ($this->getGet() !== null) {
            $json->add('get', $this->getGet());
        }

        if ($this->getPost() !== null) {
            $json->add('post', $this->getPost());
        }

        if ($this->getPut() !== null) {
            $json->add('put', $this->getPut());
        }

        if ($this->getDelete() !== null) {
            $json->add('delete', $this->getDelete());
        }

        if ($this->getPatch() !== null) {
            $json->add('patch', $this->getPatch());
        }

        return $json;
    }
}
