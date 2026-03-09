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

    public function createUser($email, $password) {
        $roleID = 2;
        $result = $this->conn->execute_query(
            "INSERT INTO Users (RoleID, Email, PasswordHash) 
            VALUES (?, ?, ?)",
            [$roleID, $email, $password]
        );

        if ($result) {
            echo "Insert successful!";
        } else {
            echo "Insert failed: " . $this->conn->error;
        }
    }

    public function createGuest($userID, $firstName, $lastName, $phone) {
        $result = $this->conn->execute_query(
            "INSERT INTO Guests (UserID, FirstName, LastName, PhoneContact) VALUES (?, ?, ?, ?)",
            [$userID, $firstName, $lastName, $phone]
        );

        if ($result) {
            echo "Guest created successfully!";
        } else {
            echo "Failed to create guest: " . $this->conn->error;
        }
    }
}

?>