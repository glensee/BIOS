<?php

class Admin {
    // property declaration
    private $name;
    private $username;
    private $password;

    public function __construct($username='', $password='', $name='') {
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getName() {
        return $this->name;
    }

    public function authenticate($enteredPwd) {
        return password_verify($enteredPwd, $this->getPassword());
    }
}

?>