<?php

class Room {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkRoomAvailability($roomID, $checkin, $checkout) {
        $this->conn->execute_query("CALL CheckRoomAvailability(?, ?, ?, @isAvailable)", [$roomID, $checkin, $checkout]);
        
        $result = $this->conn->query("SELECT @isAvailable AS available;");
        $row = $result->fetch_assoc();
        return (bool)$row['available'];
    }

    public function getRoomPrice($roomID) {
        $this->conn->execute_query("CALL GetRoomPrice(?, @price)", [$roomID]);
        
        $result = $this->conn->query("SELECT @price AS price;");
        $row = $result->fetch_assoc();
        return (float)$row['price'];
    }

    function calculateTotalAmount($basePrice, $checkin, $checkout, $numAdults = 1, $numChildren = 0) {
        // 1. Calculate number of nights
        $checkinDate = new DateTime($checkin);
        $checkoutDate = new DateTime($checkout);

        // // Ensure checkout is after checkin
        if ($checkoutDate <= $checkinDate) {
            throw new Exception("Check-out date must be after check-in date.");
        }

        $interval = $checkinDate->diff($checkoutDate);
        $numNights = $interval->days;

        // // 2. Define additional charges (optional)
        $extraAdultRate = 0.2;    // 20% extra per adult above 1
        $extraChildRate = 0.1;    // 10% extra per child

        $adultMultiplier = 1 + ($numAdults > 1 ? ($numAdults - 1) * $extraAdultRate : 0);
        $childMultiplier = $numChildren * $extraChildRate;

        $totalMultiplier = $adultMultiplier + $childMultiplier;

        // // 3. Calculate total
        $totalAmount = $basePrice * $numNights * $totalMultiplier;

        return round($totalAmount, 2);
    }
}
?>