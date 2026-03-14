<?php

class Reservation {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createReservation($guestID, $checkin, $checkout, $adults, $children, $roomID, $paymentMethod, $totalAmount) {
        try {
            $result = $this->conn->execute_query(
                "CALL CreateReservation(?, ?, ?, ?, ?, ?, ?, ?)",
                [$guestID, $checkin, $checkout, $adults, $children, $roomID, $paymentMethod, $totalAmount]
            );

            if ($result) {
                $row = $result->fetch_assoc();
                $reservationID = $row['ReservationID'] ?? null;
                $result->free();
                return $reservationID;
            } else {
                throw new Exception("Failed to create reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
}
?>