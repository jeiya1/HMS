<?php
require_once '../app/models/User.php';

class AuthController {
    public function loginForm() {
        require_once '../app/views/auth/login.view.html';
    }

    public function signupForm() {
        require_once '../app/views/auth/signup.view.html';
    }

    public function login() {

    }

    public function signup() {

    }
}

?>