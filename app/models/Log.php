<?php

class Log
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllLogs(): array
    {
        $result = $this->conn->execute_query(
            "SELECT * FROM Logs ORDER BY CreatedAt DESC"
        );

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public function getLogByID(int $id): ?array
    {
        $result = $this->conn->execute_query(
            "SELECT * FROM Logs WHERE LogID = ?",
            [$id]
        );

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function deleteLog(int $id): bool
    {
        $result = $this->conn->execute_query(
            "DELETE FROM Logs WHERE LogID = ?",
            [$id]
        );

        if (!$result) {
            throw new Exception("Failed to delete log: " . $this->conn->error);
        }

        return true;
    }

    public function clearAllLogs(): bool
    {
        $result = $this->conn->execute_query("DELETE FROM Logs");

        if (!$result) {
            throw new Exception("Failed to clear logs: " . $this->conn->error);
        }

        return true;
    }
}