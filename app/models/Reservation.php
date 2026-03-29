<?php

class Reservation
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createReservation($guestID, $checkin, $checkout, $adults, $roomID, $paymentMethod, $totalAmount)
    {
        try {
            $this->conn->execute_query(
                "CALL CreateReservation(?, ?, ?, ?, ?, ?, ?, @ReservationID, @BookingToken)",
                [$guestID, $checkin, $checkout, $adults, $roomID, $paymentMethod, $totalAmount]
            );

            $result = $this->conn->query("SELECT @ReservationID AS ReservationID, @BookingToken AS BookingToken;");


            if ($result) {
                $row = $result->fetch_assoc();
                $result->free();
                return [
                    'ReservationID' => $row['ReservationID'],
                    'BookingToken' => $row['BookingToken']
                ];
            } else {
                throw new Exception("Failed to create reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to create reservation: " . $e->getMessage());
        }
    }

    public function cancelReservation($reservationID = null, $bookingToken = null)
    {
        try {
            if ($bookingToken) {
                $result = $this->conn->execute_query(
                    "CALL CancelReservationGuest(?)",
                    [$bookingToken]
                );
            } elseif ($reservationID) {
                $result = $this->conn->execute_query(
                    "CALL CancelReservation(?)",
                    [$reservationID]
                );
            } else {
                throw new Exception("Either reservation ID or booking token must be provided.");
            }

            if (!$result) {
                throw new Exception("Failed to cancel reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to cancel reservation: " . $e->getMessage());
        }
    }

    public function addRoomToReservation($reservationID, $roomID)
    {
        try {
            $result = $this->conn->execute_query(
                "CALL AddRoomToReservation(?, ?)",
                [$reservationID, $roomID]
            );

            if (!$result) {
                throw new Exception("Failed to add room to reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to cancel reservation: " . $e->getMessage());
        }
    }

    public function getGuestReservations($GuestID)
    {
        try {
            $result = $this->conn->execute_query(
                "SELECT * FROM Reservations WHERE GuestID = ?",
                [$GuestID]
            );
            return $result;
        } catch (Exception $e) {
            throw new Exception("Failed to cancel reservation: " . $e->getMessage());
        }
    }

    public function getGuestReservationByToken($bookingToken)
    {
        try {
            $result = $this->conn->execute_query(
                "SELECT * FROM Reservations WHERE BookingToken = ?",
                [$bookingToken]
            );
            return $result;
        } catch (Exception $e) {
            throw new Exception("Failed to cancel reservation: " . $e->getMessage());
        }
    }

    public function addRoomToCart($roomID, $checkin, $checkout, $adults)
    {
        try {
            $result = $this->conn->execute_query(
                "CALL AddRoomToCart(?, ?, ?, ?)",
                [$roomID, $checkin, $checkout, $adults]
            );

            if (!$result) {
                throw new Exception("Failed to add room to cart: " . $this->conn->error);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to cancel reservation: " . $e->getMessage());
        }
    }
}