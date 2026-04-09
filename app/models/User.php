<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php'; // Adjust path to your vendor/autoload.php
require __DIR__ . '/../../env.php';

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getUserByEmail($email)
    {
        // Get user record by email
        $result = $this->conn->execute_query(
            "SELECT u.*, g.FirstName, g.LastName FROM Users u JOIN Guests g ON u.GuestID = g.GuestID WHERE u.Email = ?",
            [$email]
        );

        if ($result) {
            return $result->fetch_object();
        } else {
            throw new Exception("Query failed " . $this->conn->error);
        }
    }

    public function getUserRole($userID)
    {
        // Get the role name for the specified user
        $result = $this->conn->execute_query(
            "SELECT r.RoleName 
         FROM Roles r 
         JOIN Users u ON r.RoleID = u.RoleID 
         WHERE u.UserID = ? 
         LIMIT 1",
            [$userID]
        );

        if ($result) {
            return $result->fetch_object();
        } else {
            throw new Exception("Query failed: " . $this->conn->error);
        }
    }

    public function getGuestByEmail($email)
    {
        // Get guest record by email
        $result = $this->conn->execute_query(
            "SELECT * FROM Guests WHERE Email = ?",
            [$email]
        );

        if ($result) {
            return $result->fetch_object();
        } else {
            return false;
        }
    }

    public function getGuestIDbyUserID($userID)
    {
        // Look up guest ID for user
        $result = $this->conn->execute_query(
            "SELECT GuestID FROM Users WHERE UserID = ?",
            [$userID]
        );
        if ($result) {
            return $result->fetch_object();
        } else {
            return false;
        }
    }

    public function getGuestDetails($userID)
    {
        // Get guest details by user ID
        $result = $this->conn->execute_query(
            "SELECT g.FirstName, g.LastName, g.PhoneContact, g.BirthDate, g.Email
            FROM Guests g
            JOIN Users u ON g.GuestID = u.GuestID
            WHERE u.UserID = ?",
            [$userID]
        );
        return $result ? $result->fetch_assoc() : null;
    }

    public function createGuest($email, $firstName, $lastName, $phone, $birthDate)
    {
        // Create new guest
        $this->conn->execute_query(
            "CALL CreateGuest(?, ?, ?, ?, ?, @newGuestID)",
            [$email, $firstName, $lastName, $phone, $birthDate]
        );

        $result = $this->conn->query("SELECT @newGuestID AS GuestID;");

        return $result->fetch_assoc()['GuestID'];
    }

    public function createGuestUser($email, $emailGuest, $password, $firstName, $lastName, $phone, $birthDate)
    {
        // Check for duplicate email
        $user = $this->getUserByEmail($email);
        if ($user) {
            throw new Exception("Account with that email already exists.");
        }

        // Call stored procedure to create guest user
        $result = $this->conn->execute_query(
            "CALL CreateGuestUser(?, ?, ?, ?, ?, ?, ?)",
            [$email, $emailGuest, $password, $firstName, $lastName, $phone, $birthDate]
        );

        if (!$result) {
            throw new Exception("Failed to create account. Please try again.");
        }
    }

    public function updatePassword($newPassword)
    {
        // Check if user is logged in
        if (!isset($_SESSION['logged_in_user_id'])) {
            throw new Exception("Unauthorized: User not logged in.");
        }

        $userID = $_SESSION['logged_in_user_id'];

        // Update password in users table
        $result = $this->conn->execute_query(
            "UPDATE Users SET PasswordHash = ? WHERE UserID = ?",
            [$newPassword, $userID]
        );

        if (!$result) {
            throw new Exception("Failed to update password: " . $this->conn->error);
        }

        return true;
    }

    public function updateFirstName($newFirstName)
    {

        if (!isset($_SESSION['logged_in_user_id'])) {
            throw new Exception("Unauthorized: User not logged in.");
        }

        $userID = $_SESSION['logged_in_user_id'];

        // Get guest ID for the user
        $result = $this->conn->execute_query(
            "SELECT GuestID FROM Users WHERE UserID = ?",
            [$userID]
        );

        if (!$result || $result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        $user = $result->fetch_assoc();
        $guestID = $user['GuestID'];

        // Update guest first name
        $update = $this->conn->execute_query(
            "UPDATE Guests SET FirstName = ? WHERE GuestID = ?",
            [$newFirstName, $guestID]
        );

        if (!$update) {
            throw new Exception("Failed to update first name: " . $this->conn->error);
        }

        // Update session name
        $_SESSION['logged_in_user_name'] = $newFirstName;

        return true;
    }

    // Update last name for authenticated user
    public function updateLastName($newLastName)
    {
        // Require authentication
        if (!isset($_SESSION['logged_in_user_id'])) {
            throw new Exception("Unauthorized: User not logged in.");
        }

        $userID = $_SESSION['logged_in_user_id'];

        // Get guest ID for the user
        $result = $this->conn->execute_query(
            "SELECT GuestID FROM Users WHERE UserID = ?",
            [$userID]
        );

        if (!$result || $result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        $user = $result->fetch_assoc();
        $guestID = $user['GuestID'];

        // Update guest last name
        $update = $this->conn->execute_query(
            "UPDATE Guests SET LastName = ? WHERE GuestID = ?",
            [$newLastName, $guestID]
        );

        if (!$update) {
            throw new Exception("Failed to update last name: " . $this->conn->error);
        }

        return true;
    }

    // Update phone number for authenticated user
    public function updatePhoneNo($newPhoneNo)
    {
        if (!isset($_SESSION['logged_in_user_id'])) {
            throw new Exception("Unauthorized: User not logged in.");
        }

        $userID = $_SESSION['logged_in_user_id'];

        $result = $this->conn->execute_query(
            "SELECT GuestID FROM Users WHERE UserID = ?",
            [$userID]
        );

        if (!$result || $result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        $guestID = $result->fetch_assoc()['GuestID'];

        $update = $this->conn->execute_query(
            "UPDATE Guests SET PhoneContact = ? WHERE GuestID = ?",
            [$newPhoneNo, $guestID]
        );

        if (!$update) {
            throw new Exception("Failed to update phone contact.");
        }

        return true;
    }

    public function updateBirthdate($newBirthdate)
    {

        if (!isset($_SESSION['logged_in_user_id'])) {
            throw new Exception("Unauthorized: User not logged in.");
        }

        $userID = $_SESSION['logged_in_user_id'];

        // Get guest ID for the user
        $result = $this->conn->execute_query(
            "SELECT GuestID FROM Users WHERE UserID = ?",
            [$userID]
        );

        if (!$result || $result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        $user = $result->fetch_assoc();
        $guestID = $user['GuestID'];

        // Update birth date
        $update = $this->conn->execute_query(
            "UPDATE Guests SET BirthDate = ? WHERE GuestID = ?",
            [$newBirthdate, $guestID]
        );

        if (!$update) {
            throw new Exception("Failed to update birthdate: " . $this->conn->error);
        }

        return true;
    }

    public function updateEmail($newEmail)
    {

        if (!isset($_SESSION['logged_in_user_id'])) {
            throw new Exception("Unauthorized: User not logged in.");
        }

        $userID = $_SESSION['logged_in_user_id'];

        // Check for duplicate email
        $check = $this->conn->execute_query(
            "SELECT UserID FROM Users WHERE Email = ?",
            [$newEmail]
        );

        if ($check && $check->num_rows > 0) {
            throw new Exception("Email already in use.");
        }

        // Update email
        $result = $this->conn->execute_query(
            "UPDATE Users SET Email = ? WHERE UserID = ?",
            [$newEmail, $userID]
        );

        if (!$result) {
            throw new Exception("Failed to update email: " . $this->conn->error);
        }

        return true;
    }

    public function updatePasswordByID($userID, $newPassword)
    {
        // Update password by id
        $result = $this->conn->execute_query(
            "UPDATE Users SET PasswordHash = ? WHERE UserID = ?",
            [$newPassword, $userID]
        );

        if (!$result) {
            throw new Exception("Failed to update password: " . $this->conn->error);
        }
    }

    public function createPasswordResetToken($userID)
    {
        // Generate secure token
        $token = bin2hex(random_bytes(16)); // secure token
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $tokenHash = hash('sha256', $token); // store hashed token for security

        // Insert token into database
        $result = $this->conn->execute_query(
            "INSERT INTO PasswordResets (UserID, Token, ExpiresAt) VALUES (?, ?, ?)",
            [$userID, $tokenHash, $expires]
        );

        if (!$result) {
            throw new Exception("Failed to create password reset token: " . $this->conn->error);
        }

        return $token; // return the token so controller can send email
    }

    public function sendPasswordResetEmail($toEmail, $token)
    {
        // Configure email sender
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $resetLink = "http://hms.local/reset-submit?token=$token";
            $mail->Body = "Click this link to reset your password (valid 30 min): <a href='$resetLink'>$resetLink</a>";
            $mail->AltBody = "Click this link to reset your password (valid 30 min): $resetLink";

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception("Mailer: " . $mail->ErrorInfo);
        }
    }
    public function getUserByResetToken($token)
    {
        // Validate token and retrieve user
        $now = date('Y-m-d H:i:s');
        $tokenHash = hash('sha256', $token);
        $result = $this->conn->execute_query(
            "SELECT u.* FROM Users u
         JOIN PasswordResets p ON u.UserID = p.UserID
         WHERE p.Token = ? AND p.ExpiresAt > ?",
            [$tokenHash, $now]
        );

        if ($result && $result->num_rows > 0) {
            return $result->fetch_object();
        }
        return null;
    }

    public function deleteResetToken($token)
    {
        // Remove reset token from database
        $this->conn->execute_query(
            "DELETE FROM PasswordResets WHERE Token = ?",
            [$token]
        );
    }

    public function getUserByID($userID)
    {
        // Get user through id
        $result = $this->conn->execute_query(
            "SELECT * FROM Users WHERE UserID = ?",
            [$userID]
        );

        return $result->fetch_object();
    }

    public function updateFullName($fname, $lname)
    {
        // Get user ID from session
        $userID = $_SESSION['logged_in_user_id'];

        // Update both first and last name
        $result = $this->conn->execute_query(
            "UPDATE Guests g
            JOIN Users u ON g.GuestID = u.GuestID
            SET g.FirstName = ?, g.LastName = ?
            WHERE u.UserID = ?",
            [$fname, $lname, $userID]
        );

        if (!$result) {
            throw new Exception("Failed to update name.");
        }

        $_SESSION['logged_in_user_name'] = $fname;
    }

    public function updateGuestDetails($phone, $birthDate)
    {
        // Get user ID from session
        $userID = $_SESSION['logged_in_user_id'];

        // Update guest contact info
        $result = $this->conn->execute_query(
            "UPDATE Guests g
            JOIN Users u ON g.GuestID = u.GuestID
            SET g.PhoneContact = ?, g.BirthDate = ?
            WHERE u.UserID = ?",
            [$phone, $birthDate, $userID]
        );

        if (!$result) {
            throw new Exception("Failed to update guest details.");
        }
    }
    // =========================
    // Check if a guest exists by email
    // =========================
    public function getGuestEmailByID($guestID)
    {
        $result = $this->conn->execute_query(
            "SELECT Email FROM Guests WHERE GuestID = ?",
            [$guestID]
        );

        if ($result && $result->num_rows > 0) {
            return $result->fetch_object()->Email;
        }
        return null;
    }

    // =========================
    // Link a reservation to a user
    // =========================
    public function linkReservationToUser($reservationID, $userID)
    {
        $result = $this->conn->execute_query(
            "INSERT INTO UserReservations (UserID, ReservationID) VALUES (?, ?)",
            [$userID, $reservationID]
        );

        if (!$result) {
            throw new Exception("Failed to link reservation to user: " . $this->conn->error);
        }

        return true;
    }

    public function isReservationLinkedToUser($reservationID, $userID)
    {
        // Check if reservation is linked to user
        $result = $this->conn->execute_query(
            "SELECT 1 FROM UserReservations WHERE ReservationID = ? AND UserID = ?",
            [$reservationID, $userID]
        );

        return $result && $result->num_rows > 0;
    }


}
?>