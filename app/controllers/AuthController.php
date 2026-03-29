<?php
require_once '../app/models/User.php';

class AuthController
{
    public function showResetForm($token = null)
    {
        if (!$token) {
            // Render the auth view and show toast for invalid token
            require_once '../app/views/auth/auth.view.php';
            echo "<script>showToast('Invalid or missing token.', 'error');</script>";
            exit;
        }

        $userModel = new User($GLOBALS['conn']);
        $user = $userModel->getUserByResetToken($token);

        if (!$user) {
            require_once '../app/views/auth/auth.view.php';
            echo "<script>showToast('Token is invalid or expired.', 'error');</script>";
            exit;
        }

        // Token is valid — show reset password form
        require_once '../app/views/auth/reset_password.view.php';
    }

    public function login()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                echo json_encode([
                    "success" => false,
                    "error" => "Please enter both email and password."
                ]);
                exit;
            }

            try {
                $userModel = new User($GLOBALS['conn']);
                $user = $userModel->getUserByEmail($email);

                if ($user && password_verify($password, $user->PasswordHash)) {
                    $_SESSION['logged_in_user_id'] = $user->UserID;
                    $_SESSION['logged_in_user_name'] = $user->FirstName;

                    echo json_encode([
                        "success" => true,
                        "message" => "Login successful!",
                        "redirect" => "/home"
                    ]);
                    exit;
                } else {
                    echo json_encode([
                        "success" => false,
                        "error" => "Invalid email or password."
                    ]);
                    exit;
                }
            } catch (Exception $e) {
                error_log($e->getMessage());

                echo json_encode([
                    "success" => false,
                    "error" => "Something went wrong. Please try again."
                ]);
                exit;
            }
        }
    }

    public function signup()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = trim($_POST['fname']);
            $lname = trim($_POST['lname']);
            $phone = trim($_POST['phone']);
            $birthDate = trim($_POST['birthDate']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($birthDate)) {
                echo json_encode(["success" => false, "error" => "Please fill in all fields."]);
                exit;
            }

            // Check if underage
            $dob = new DateTime($birthDate);
            $today = new DateTime();
            $age = $today->diff($dob)->y;
            if ($age < 18) {
                echo json_encode(["success" => false, "error" => "You must be at least 18 years old to register."]);
                exit;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userModel = new User($GLOBALS['conn']);

            if ($userModel->getUserByEmail($email)) {
                echo json_encode([
                    "success" => false,
                    "error" => "Email already exists."
                ]);
                exit;
            }

            try {
                $userModel->createGuestUser($email, $email, $hash, $fname, $lname, $phone, $birthDate);

                $user = $userModel->getUserByEmail($email);

                // Auto-login
                $_SESSION['logged_in_user_id'] = $user->UserID;
                $_SESSION['logged_in_user_name'] = $user->FirstName;

                echo json_encode([
                    "success" => true,
                    "message" => "Signup successful!",
                    "redirect" => "/home"
                ]);
                exit;
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo json_encode([
                    "success" => false,
                    "error" => $e->getMessage()
                ]);
                exit;
            }
        }
    }
    public function logout()
    {
        session_destroy();
        echo json_encode(["success" => true, "redirect" => "/home"]);
        exit;
    }


    public function resetPasswordForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);

            $userModel = new User($GLOBALS['conn']);
            $user = $userModel->getUserByEmail($email);

            if ($user) {
                $token = $userModel->createPasswordResetToken($user->UserID);

                if ($userModel->sendPasswordResetEmail($user->Email, $token)) {
                    echo json_encode(["success" => true, "message" => "Recovery link sent!"]);
                } else {
                    echo json_encode(["success" => false, "error" => "Failed to send email."]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "Email not found in our system."]);
            }
        }
    }
    public function sendResetLinkk()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $userModel = new User($GLOBALS['conn']);
            $user = $userModel->getUserByEmail($email);

            if ($user) {
                $token = $userModel->createPasswordResetToken($user['UserID']);
                if ($userModel->sendPasswordResetEmail($user['Email'], $token)) {
                    echo json_encode(["success" => true, "message" => "Recovery link sent!"]);
                } else {
                    echo json_encode(["success" => false, "error" => "Failed to send email."]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "Email not found in our system."]);
            }
        }
    }
    public function resetPassword()
    {
        $token = $_POST['token'] ?? null;

        if (!$token) {
            echo json_encode(["success" => false, "error" => "Missing token."]);
            exit;
        }

        $userModel = new User($GLOBALS['conn']);
        $user = $userModel->getUserByResetToken($token);

        if (!$user) {
            echo json_encode(["success" => false, "error" => "Invalid or expired token."]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = trim($_POST['password']);

            if (
                strlen($newPassword) < 8
                || !preg_match('/[A-Z]/', $newPassword)
                || !preg_match('/[a-z]/', $newPassword)
                || !preg_match('/[0-9]/', $newPassword)
                || !preg_match('/[\W]/', $newPassword)
            ) {
                echo json_encode(["success" => false, "error" => "Password must be at least 8 chars, include upper/lowercase, number, and special char."]);
                exit;
            }

            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $userModel->updatePasswordByID($user->UserID, $hash);
            $userModel->deleteResetToken($token);

            echo json_encode(["success" => true, "message" => "Password updated successfully!", "redirect" => "/registration"]);
            exit;
        }
    }
}

?>