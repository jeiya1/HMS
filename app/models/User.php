<?php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByEmail($email) {
        $result = $this->conn->execute_query(
            "SELECT * FROM Users WHERE Email = ?",
            [$email]
        );

        if ($result) {
            return $result->fetch_object();
        } else {
            echo "Query failed: " . $this->conn->error;
            return null;
        }
    }

    public function getGuestByEmail($email) {
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

    public function createGuest($email, $firstName, $lastName, $phone) {
        // TODO: check if a user with the same email already exists in Users or Guests table before creating a new guest

        // Check if guest already exists by email
        $existingGuest = $this->getGuestByEmail($email);
        if ($existingGuest) {
            return $existingGuest->GuestID;
        }

        // Create new guest if not exists
        $result = $this->conn->execute_query(
            "CALL CreateGuest(?, ?, ?, ?, ?)",
            [NULL, $email, $firstName, $lastName, $phone]
        );

        if ($result) {
            // Return the new GuestID
            return $this->conn->insert_id;
        } else {
            throw new Exception("Failed to create guest: " . $this->conn->error);
        }
    }

    public function createGuestUser($email, $emailGuest, $password, $firstName, $lastName, $phone) {
        $result = $this->conn->execute_query(
            "CALL CreateGuestUser(?, ?, ?, ?, ?, ?)",
            [$email, $emailGuest, $password, $firstName, $lastName, $phone]
        );

        if ($result) {
            echo "Guest created successfully!";
        } else {
            echo "Failed to create guest: " . $this->conn->error;
        }
    }

    public function updatePassword($email, $newPassword) {
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
}

?>