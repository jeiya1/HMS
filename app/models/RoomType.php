<?php

class RoomType {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Retrieve all room types from database
    function getAllRoomTypes() {
        $result = $this->conn->execute_query("SELECT RoomTypeID, RoomTypeName FROM RoomTypes");
        $roomTypes = [];
        while ($row = $result->fetch_assoc()) {
            $roomTypes[] = $row;
        }
        return $roomTypes;
    }
}
?>