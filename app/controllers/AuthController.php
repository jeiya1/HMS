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

            $userModel = new User($GLOBALS['conn']);
            $user = $userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user->PasswordHash)) {
                $_SESSION['user_id'] = $user->UserID;
                $_SESSION['role'] = $user->RoleName;
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
            // TODO: Verify all the fields 

            $userModel = new User($GLOBALS['conn']);
            $userModel->createUser($email, $hash);

            $userModel->createGuest($userModel->getUserByEmail($email)->UserID, $fname, $lname, $phone);

            header('Location: /login');
            exit;
        }
    }
}

?>