<?php

namespace webfiori\tests\http\testServices;

class TestUserObj {
    private $name;
    private $id;
    private $username;
    
    public function setId($id) {
        $this->id = $id;
    }
    public function setFullName($name) {
        $this->name = $name;
    }
    public function setUserName($u) {
        $this->username = $u;
    }
    public function getId() {
        return $this->id;
    }
    public function getFullName() {
        return $this->name;
    }
    public function getUsername() {
        return $this->username;
    }
}
