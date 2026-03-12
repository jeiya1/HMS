<?php

class Reservation {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createReservation($guestID, $statusID, $checkin, $checkout, $adults, $children) {

        $result = $this->conn->execute_query(
            "INSERT INTO Reservations
            (GuestID, StatusID, CheckInDate, CheckOutDate, NumAdults, NumChildren)
            VALUES (?, ?, ?, ?, ?, ?)",
            [$guestID, $statusID, $checkin, $checkout, $adults, $children]
        );

        if ($result) {

            $reservationID = $this->conn->insert_id;

            echo "Reservation created successfully!";
            return $reservationID;

        } else {

            echo "Failed to create reservation: " . $this->conn->error;
            return null;

        }
    }


    public function assignRoom($reservationID, $roomID){//For reservation room transaction

        $result = $this->conn->execute_query(
            "INSERT INTO ReservationRooms (ReservationID, RoomID)
            VALUES (?, ?)",
            [$reservationID, $roomID]
        );

        if ($result) {
            echo "Room assigned successfully!";
        } else {
            echo "Failed to assign room: " . $this->conn->error;
        }

    }

}
?>