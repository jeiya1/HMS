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

        // Validate token and show reset form
        if (!$user) {
            require_once '../app/views/auth/auth.view.php';
            echo "<script>showToast('Token is invalid or expired.', 'error');</script>";
            exit;
        }

        require_once '../app/views/auth/reset_password.view.php';
    }

    public function login()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // Validate email and password are not empty
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

                // Verify user exists and password matches
                if ($user && password_verify($password, $user->PasswordHash)) {
                    // Store user info in session and redirect to home
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
            $country = trim($_POST['country_code']);
            $phoneNo = trim($_POST['phone']);
            $phone = $country . $phoneNo;
            $birthDate = trim($_POST['birthDate']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // Checks if the required fields are filled
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

            // Check if email is already registered
            if ($userModel->getUserByEmail($email)) {
                echo json_encode([
                    "success" => false,
                    "error" => "Email already exists."
                ]);
                exit;
            }

            try {
                // Create new user account
                $userModel->createGuestUser($email, $email, $hash, $fname, $lname, $phone, $birthDate);

                $user = $userModel->getUserByEmail($email);

                // Automatically log in the new user and redirect to home
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
        // Destroy session and redirect to home
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

            // If user exists, create reset token and send an email
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

            // Create and send password reset token if user exists
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

        // Validate if reset token is provided
        if (!$token) {
            echo json_encode(["success" => false, "error" => "Missing token."]);
            exit;
        }

        $userModel = new User($GLOBALS['conn']);
        $user = $userModel->getUserByResetToken($token);

        // Checks if token is still valid and associated with a user
        if (!$user) {
            echo json_encode(["success" => false, "error" => "Invalid or expired token."]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = trim($_POST['password']);

            // Validate password strength
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

            // Update password and clear reset token
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $userModel->updatePasswordByID($user->UserID, $hash);
            $userModel->deleteResetToken($token);

            echo json_encode(["success" => true, "message" => "Password updated successfully!", "redirect" => "/registration"]);
            exit;
        }
    }

    public function updateProfile()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["success" => false, "error" => "Invalid request."]);
            exit;
        }

        // Verify if user is authenticated
        if (!isset($_SESSION['logged_in_user_id'])) {
            echo json_encode(["success" => false, "error" => "Unauthorized."]);
            exit;
        }

        $userID = $_SESSION['logged_in_user_id'];

        $fname = trim($_POST['fname'] ?? '');
        $lname = trim($_POST['lname'] ?? '');
        $country = trim($_POST['country_code'] ?? '');
        $phoneNo = trim($_POST['phone'] ?? '');
        $birthDate = trim($_POST['birthDate'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = trim($_POST['passwordc'] ?? '');
        $newPassword = trim($_POST['password'] ?? '');

        try {

            $userModel = new User($GLOBALS['conn']);
            $user = $userModel->getUserByID($userID);
            $guest = $userModel->getGuestDetails($userID);

            if (!$user) {
                echo json_encode(["success" => false, "error" => "User not found."]);
                exit;
            }

            // If new password is provided, current password must be verified
            $requiresPassword = !empty($newPassword);

            if ($requiresPassword) {
                if (empty($currentPassword) || !password_verify($currentPassword, $user->PasswordHash)) {
                    echo json_encode(["success" => false, "error" => "Current password is incorrect."]);
                    exit;
                }
            }

            // Update fields if they have values
            if (!empty($fname))
                $userModel->updateFirstName($fname);
            if (!empty($lname))
                $userModel->updateLastName($lname);
            $currentPhone = $guest['PhoneContact'] ?? '';
            $finalPhone = $currentPhone;

            $countryCodes = ['+63', '+1', '+44', '+61'];

            // Case 1: user entered new local number
            if (!empty($phoneNo)) {
                $finalPhone = $country . $phoneNo;
            }
            // Case 2: user changed country code only
            elseif (!empty($currentPhone) && empty($phoneNo)) {
                $localPart = $currentPhone;

                // Detect old country code and remove it
                foreach ($countryCodes as $code) {
                    if (str_starts_with($currentPhone, $code)) {
                        $localPart = substr($currentPhone, strlen($code));
                        break;
                    }
                }

                // Prepend new country code
                $finalPhone = $country . $localPart;
            }

            // Only update if changed
            if ($finalPhone !== $currentPhone) {
                $userModel->updatePhoneNo($finalPhone);
            }
            if (!empty($birthDate))
                $userModel->updateBirthdate($birthDate);
            if (!empty($email))
                $userModel->updateEmail($email);

            // Validate password strength and updates it
            if (!empty($newPassword)) {
                if (
                    strlen($newPassword) < 8 ||
                    !preg_match('/[A-Z]/', $newPassword) ||
                    !preg_match('/[a-z]/', $newPassword) ||
                    !preg_match('/[0-9]/', $newPassword) ||
                    !preg_match('/[\W]/', $newPassword)
                ) {
                    echo json_encode(["success" => false, "error" => "Weak password."]);
                    exit;
                }

                $hash = password_hash($newPassword, PASSWORD_DEFAULT);

                $userModel->updatePassword($hash);
            }

            echo json_encode([
                "success" => true,
                "message" => "Profile updated successfully!"
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
            exit;
        }
    }

    public function getProfile()
    {
        header('Content-Type: application/json');

        // Verify user if authenticated
        if (!isset($_SESSION['logged_in_user_id'])) {
            echo json_encode(["success" => false, "error" => "Unauthorized"]);
            exit;
        }

        $userID = $_SESSION['logged_in_user_id'];
        $userModel = new User($GLOBALS['conn']);
        // Get guest profile data
        $guestData = $userModel->getGuestDetails($userID);

        if (!$guestData) {
            echo json_encode(["success" => false, "error" => "User not found"]);
            exit;
        }

        echo json_encode([
            "success" => true,
            "data" => $guestData
        ]);
        exit;
    }
}

?>