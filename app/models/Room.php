<?php

class Room
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function checkRoomAvailability($roomID, $checkin, $checkout)
    {
        $this->conn->execute_query("CALL CheckRoomAvailability(?, ?, ?, @isAvailable)", [$roomID, $checkin, $checkout]);

        $result = $this->conn->query("SELECT @isAvailable AS available;");
        $row = $result->fetch_assoc();
        return (bool) $row['available'];
    }

    public function getRoomPrice($roomID)
    {
        $this->conn->execute_query("CALL GetRoomPrice(?, @price)", [$roomID]);

        $result = $this->conn->query("SELECT @price AS price;");
        $row = $result->fetch_assoc();
        return (float) $row['price'];
    }

    function calculateTotalAmount($roomTypeName, $basePrice, $checkin, $checkout, $numAdults = 1)
    {
        // 1. Calculate number of nights
        $checkinDate = new DateTime($checkin);
        $checkoutDate = new DateTime($checkout);

        if ($checkoutDate < $checkinDate) {
            throw new Exception("Check-out date must be after check-in date.");
        }

        $interval = $checkinDate->diff($checkoutDate);
        $numNights = $interval->days;

        if ($numNights == 0) {
            $numNights = 1; // Minimum charge for 1 night
        }

        $totalAmount = $basePrice * $numNights;

        // 3. Discount for > 3 nights
        if ($numNights > 3) {
            $totalAmount *= 0.85; // Apply 15% discount

            // 4. Additional guest charge
            // Determine minimum occupancy based on room type
            $occupancy = 1; // default single occupancy
            if (stripos($roomTypeName, 'Double') !== false) {
                $occupancy = 2; // minimum 2 guests for double rooms
            }

            // Total guests
            $totalGuests = max($numAdults, $occupancy);

            // Extra charge = 10% of room rate per guest × number of nights
            $extraCharge = $basePrice * 0.10 * $totalGuests * $numNights;
            $totalAmount += $extraCharge;
        }

        // 5. Apply VAT (12%)
        $totalAmount *= 1.12;

        return round($totalAmount, 2);
    }

    function getRoomTypeName($roomID)
    {
        // Call stored procedure
        $this->conn->execute_query("CALL GetRoomName(?, @name)", [$roomID]);

        // 🔴 VERY IMPORTANT: clear remaining result sets
        while ($this->conn->more_results() && $this->conn->next_result()) {
            $this->conn->use_result();
        }

        // Fetch the output variable
        $result = $this->conn->query("SELECT @name AS name");
        $row = $result->fetch_assoc();

        return $row['name'] ?? null;
    }

    public function searchAvailable($filters)
    {
        $result = $this->conn->execute_query(
            "CALL SearchAvailableRooms(?, ?, ?, ?, ?, ?)",
            [
                $filters['checkin'] ?? null,
                $filters['checkout'] ?? null,
                $filters['adults'] ?? null,
                $filters['children'] ?? null,
                $filters['room'] ?? null,       // single/double
                $filters['room_type'] ?? null   // Standard/Deluxe/Suite
            ]
        );

        $rooms = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
            $result->free();
        }

        // Clear remaining result sets
        while ($this->conn->more_results() && $this->conn->next_result()) {
            $extraResult = $this->conn->use_result();
            if ($extraResult instanceof mysqli_result) {
                $extraResult->free();
            }
        }

        return $rooms;
    }
}
?>