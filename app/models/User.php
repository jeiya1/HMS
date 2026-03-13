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

    public function createGuest($firstName, $lastName, $phone) {
        $result = $this->conn->execute_query(
            "CALL CreateGuest(?, ?, ?, ?)",
            [$firstName, $lastName, $phone]
        );

        if ($result) {
            echo "Guest created successfully!";
        } else {
            echo "Failed to create guest: " . $this->conn->error;
        }
    }

    public function createGuestUser($email, $password, $firstName, $lastName, $phone) {
        $result = $this->conn->execute_query(
            "CALL CreateGuestUser(?, ?, ?, ?, ?)",
            [$email, $password, $firstName, $lastName, $phone]
        );

        if ($result) {
            echo "Guest created successfully!";
        } else {
            echo "Failed to create guest: " . $this->conn->error;
        }
    }
}

?>