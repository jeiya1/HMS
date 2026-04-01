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

    public function showReservations()
    {
        try {
            $userID = $_SESSION["logged_in_user_id"];

            $result = $this->conn->execute_query(
                "SELECT
                r.ReservationID,
                r.BookingToken,
                rs.StatusName AS ReservationStatus,
                pm.MethodName AS PaymentMethod,
                p.Amount,
                p.PaymentDate,
                r.CreatedAt,
                g.FirstName AS GuestFirstName,
                g.LastName AS GuestLastName,
                g.Email AS GuestEmail
            FROM Reservations r
            INNER JOIN Guests g ON r.GuestID = g.GuestID
            INNER JOIN ReservationStatus rs ON r.StatusID = rs.StatusID
            LEFT JOIN Payments p ON r.ReservationID = p.ReservationID
            LEFT JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
            INNER JOIN UserReservations ur ON r.ReservationID = ur.ReservationID
            WHERE ur.UserID = ?",
                [$userID]
            );

            if (!$result) {
                throw new Exception("Error retrieving reservations: " . $this->conn->error);
            }

            if ($result->num_rows === 0) {
                return [];
            }

            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Error retrieving reservations: " . $e->getMessage());
        }
    }

    public function getReservationsForUser($userID)
    {
        $result = $this->conn->execute_query(
            "SELECT r.* 
         FROM Reservations r
         JOIN UserReservations ur ON r.ReservationID = ur.ReservationID
         WHERE ur.UserID = ?",
            [$userID]
        );

        $reservations = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }

        return $reservations;
    }
    public function linkReservationToUser($reservationID, $userID)
    {
        $result = $this->conn->execute_query(
            "INSERT INTO UserReservations (UserID, ReservationID) VALUES (?, ?)",
            [$userID, $reservationID]
        );

        if (!$result) {
            throw new Exception("Failed to link reservation to user: " . $this->conn->error);
        }

        return true;
    }

    public function isReservationLinkedToUser($reservationID, $userID)
    {
        $result = $this->conn->execute_query(
            "SELECT 1 FROM UserReservations WHERE ReservationID = ? AND UserID = ?",
            [$reservationID, $userID]
        );

        return $result && $result->num_rows > 0;
    }

    public function findByToken($token)
    {
        $result = $this->conn->execute_query(
            "SELECT * FROM Reservations WHERE BookingToken = ?",
            [$token]
        );

        return $result->fetch_assoc();
    }

    public function getReservationRooms($token)
    {
        $result = $this->conn->execute_query(
            "SELECT
                res.ReservationID,
                rt.RoomTypeName AS RoomType,
                r.RoomNumber,
                rr.NumAdults AS NumGuests,
                rt.BasePrice,
                rr.CheckInDate,
                rr.CheckOutDate
            FROM Reservations res
            JOIN ReservationRooms rr ON rr.ReservationID = res.ReservationID
            JOIN Rooms r ON r.RoomID = rr.RoomID
            JOIN RoomTypes rt ON rt.RoomTypeID = r.RoomTypeID
            WHERE res.BookingToken = ?",
            [$token]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function checkUserReservation($userID, $reservationID)
    {
        $result = $this->conn->execute_query(
            "SELECT 1 FROM UserReservations WHERE UserID = ? AND ReservationID = ? LIMIT 1",
            [$userID, $reservationID]
        );
        return $result && $result->num_rows > 0;
    }

    public function getReservationWithGuest($token)
    {
        $result = $this->conn->execute_query(
            "SELECT
            res.ReservationID,
            res.BookingToken,
            res.CreatedAt AS BookingDate,
            g.FirstName,
            g.LastName,
            g.Email,
            g.PhoneContact,
            rr.CheckInDate,
            rr.CheckOutDate,
            rr.NumAdults AS NumGuests,
            r.RoomNumber,
            rt.RoomTypeName AS RoomType,
            rt.BasePrice
        FROM Reservations res
        JOIN Guests g ON g.GuestID = res.GuestID
        JOIN ReservationRooms rr ON rr.ReservationID = res.ReservationID
        JOIN Rooms r ON r.RoomID = rr.RoomID
        JOIN RoomTypes rt ON rt.RoomTypeID = r.RoomTypeID
        WHERE res.BookingToken = ?",
            [$token]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getReservationPayment($reservationID)
    {
        $result = $this->conn->execute_query(
            "SELECT p.PaymentID, pm.MethodName AS PaymentMethod, p.PaymentStatus
         FROM Payments p
         JOIN PaymentMethods pm ON pm.MethodID = p.MethodID
         WHERE p.ReservationID = ? LIMIT 1",
            [$reservationID]
        );
        return $result->fetch_assoc();
    }
}