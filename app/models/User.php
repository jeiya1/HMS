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
        $result = $this->conn->execute_query(
            "SELECT u.*, g.FirstName, g.LastName FROM Users u JOIN Guests g ON u.GuestID = g.GuestID WHERE u.Email = ?",
            [$email]
        );

        if ($result) {
            return $result->fetch_object();
        } else {
            echo "Query failed: " . $this->conn->error;
            return null;
        }
    }

    public function getGuestByEmail($email)
    {
        $result = $this->conn->execute_query(
            "SELECT * FROM Guests WHERE Email = ?",
            [$email]
        );

        if ($result) {
            return $result->fetch_object();
        } else {
            echo "Query failed: " . $this->conn->error;
            return null;
        }
    }

    public function createGuest($email, $firstName, $lastName, $phone)
    {
        // Check if guest already exists
        $existingGuest = $this->getGuestByEmail($email);
        if ($existingGuest) {
            return $existingGuest->GuestID;
        }

        // Create new guest
        $this->conn->execute_query(
            "CALL CreateGuest(?, ?, ?, ?)",
            [$email, $firstName, $lastName, $phone]
        );

        return $this->conn->insert_id;
    }

    public function createGuestUser($email, $emailGuest, $password, $firstName, $lastName, $phone)
    {
        $result = $this->conn->execute_query(
            "CALL CreateGuestUser(?, ?, ?, ?, ?, ?)",
            [$email, $emailGuest, $password, $firstName, $lastName, $phone]
        );

        if (!$result) {
            throw new Exception("Failed to create user: " . $this->conn->error);
        }

        return true;
    }

    public function updatePassword($email, $newPassword)
    {
        $result = $this->conn->execute_query(
            "UPDATE Users SET PasswordHash = ? WHERE Email = ?",
            [$newPassword, $email]
        );

        if ($result) {
            echo "Password updated successfully!";
            return true;
        } else {
            echo "Failed to update password: " . $this->conn->error;
            return false;
        }
    }

    public function updatePasswordByID($userID, $newPassword)
    {
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
        $token = bin2hex(random_bytes(16)); // secure token
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $tokenHash = hash('sha256', $token); // store hashed token for security

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
            $resetLink = "http://hms.local/password-reset?token=$token";
            $mail->Body = "Click this link to reset your password (valid 30 min): <a href='$resetLink'>$resetLink</a>";
            $mail->AltBody = "Click this link to reset your password (valid 30 min): $resetLink";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // log or display error
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
    public function getUserByResetToken($token)
    {
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
        $this->conn->execute_query(
            "DELETE FROM PasswordResets WHERE Token = ?",
            [$token]
        );
    }
}

?>