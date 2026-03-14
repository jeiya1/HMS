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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                echo "Please enter both email and password.";
                return;
            }

            $userModel = new User($GLOBALS['conn']);
            $user = $userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user->PasswordHash)) {
                $_SESSION['logged_in_user_id'] = $user->UserID;
                header('Location: /home');
                exit;
            } else {
                echo "Invalid email or password.";
            }
        }
    }

    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $passwordr = trim($_POST['passwordr']);
            
            if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($passwordr)) {
                echo "Please fill in all fields.";
                return;
            }

            if ($password !== $passwordr) {
                echo "Passwords do not match.";
                return;
            }

            if (strlen($password) < 8) {
                echo "Password must be at least 8 characters long.";
                return;
            }
    
            if (!preg_match('/[A-Z]/', $password)) {
                echo "Password must contain at least one uppercase letter.";
                return;
            }
    
            if (!preg_match('/[a-z]/', $password)) {
                echo "Password must contain at least one lowercase letter.";
                return;
            }
    
            if (!preg_match('/[0-9]/', $password)) {
                echo "Password must contain at least one number.";
                return;
            }
    
            if (!preg_match('/[\W]/', $password)) {
                echo "Password must contain at least one special character.";
                return;
            }
            
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $userModel = new User($GLOBALS['conn']);

            $userModel->createGuestUSER($email, $hash, $fname, $lname, $phone);

            header('Location: /login');
            exit;
        }
    }
    public function logout() {
        session_destroy();
        header('Location: /home');
        exit;
    }
}

?>