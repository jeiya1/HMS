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

    public function cancelReservation($reservationID) {
        try {
            $result = $this->conn->execute_query(
                "CALL CancelReservation(?)",
                [$reservationID]
            );

            if ($result) {
                echo "Reservation cancelled successfully.";
            } else {
                throw new Exception("Failed to cancel reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addRoomToReservation($reservationID, $roomID) {
        try {
            $result = $this->conn->execute_query(
                "CALL AddRoomToReservation(?, ?)",
                [$reservationID, $roomID]
            );

            if ($result) {
                echo "Room added to reservation successfully.";
            } else {
                throw new Exception("Failed to add room to reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getGuestReservations($GuestID) {
        try {
            $result = $this->conn->execute_query(
                "SELECT * FROM Reservations WHERE GuestID = ?",
                [$GuestID]
            );
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
}