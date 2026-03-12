<?php

class Room {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkRoomAvailability($roomID, $checkin, $checkout) {

        $result = $this->conn->execute_query(
            "SELECT * FROM ReservationRooms rr
            JOIN Reservations r ON rr.ReservationID = r.ReservationID
            WHERE rr.RoomID = ?
            AND (
                (? BETWEEN r.CheckInDate AND r.CheckOutDate)
                OR
                (? BETWEEN r.CheckInDate AND r.CheckOutDate)
                OR
                (r.CheckInDate BETWEEN ? AND ?)
            )",
            [$roomID, $checkin, $checkout, $checkin, $checkout]
        );

        if ($result) {
            if ($result->num_rows > 0) {
                return false; // room already booked
            } else {
                return true; // room available
            }
        } else {
            echo "Query failed: " . $this->conn->error;
            return false;
        }

    }

}
?>