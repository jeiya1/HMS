<?php

class Reservation
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createReservation($guestID, $paymentMethod, $totalAmount)
    {
        try {
            $this->conn->execute_query(
                "CALL CreateReservation(?, ?, ?, @ReservationID, @BookingToken)",
                [$guestID, $paymentMethod, $totalAmount]
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

    public function addRoomToReservation($reservationID, $roomID, $checkIn, $checkOut, $numAdults)
    {
        try {
            $result = $this->conn->execute_query(
                "CALL AddRoomToReservation(?, ?, ?, ?, ?)",
                [$reservationID, $roomID, $checkIn, $checkOut, $numAdults]
            );

            if (!$result) {
                throw new Exception("Failed to add room to reservation: " . $this->conn->error);
            }

            return true;

        } catch (Exception $e) {
            throw new Exception("Failed to add room to reservation: " . $e->getMessage());
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

    public function showReservations() {
        try {
            $result = $this->conn->execute_query(
                "SELECT
                    r.ReservationID,
                    r.BookingToken,
                    rs.StatusName AS ReservationStatus,
                    pm.MethodName AS PaymentMethod,
                    p.Amount,
                    p.PaymentDate,
                    r.CreatedAt
                FROM Users u
                INNER JOIN Guests g ON u.GuestID = g.GuestID
                INNER JOIN Reservations r ON g.GuestID = r.GuestID
                INNER JOIN ReservationStatus rs ON r.StatusID = rs.StatusID
                LEFT JOIN Payments p ON r.ReservationID = p.ReservationID
                LEFT JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
                WHERE u.UserID = ?",
                [$_SESSION["logged_in_user_id"]]
            );

            if (!$result) {
                throw new Exception("Error retrieving reservations: ". $this->conn->error);
            }

            if ($result->num_rows === 0) {
                return [];
            }
        
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error retrieving reservations: ". $e->getMessage());
        }
    }
}