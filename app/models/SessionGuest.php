<?php

class SessionGuest
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Find or create session guest by token
    public function findOrCreate($token)
    {
        // Check if session guest exists
        $result = $this->conn->execute_query(
            "SELECT SessionGuestID 
             FROM SessionGuests 
             WHERE SessionToken = ? 
             LIMIT 1",
            [$token]
        );

        if ($result === false) {
            return;
        }

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row["SessionGuestID"]) {
                return $row["SessionGuestID"];
            }
        }

        // Create new session guest if not found
        $result = $this->conn->execute_query(
            "INSERT INTO SessionGuests (SessionToken, ExpiresAt) VALUES (?,?)",
            [$token, date('Y-m-d H:i:s', strtotime('+1 day'))]
        );

        if ($result === true) {
            return $this->conn->insert_id;
        }

    }

    // Retrieve session guest by ID
    public function getById($id)
    {
        // Check if session guest exists
        $result = $this->conn->execute_query(
            "SELECT * 
             FROM SessionGuests 
             WHERE SessionGuestID = ? 
             LIMIT 1",
            [$id]
        );

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row["SessionGuestID"];

        } else {
            return null;
        }
    }
}