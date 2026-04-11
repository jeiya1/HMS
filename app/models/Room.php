<?php

class Room
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ─── Availability / Search (unchanged) ───────────────────────────────────

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

    public function getRoomInfoByRoomNumber($roomNumber)
    {
        $result = $this->conn->execute_query(
            "SELECT rt.RoomTypeName, rt.BasePrice, rt.MaxOccupancy
             FROM Rooms r
             JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
             WHERE r.RoomNumber = ?",
            [$roomNumber]
        );
        return $result->fetch_assoc() ?: null;
    }

    public function getRoomTypeName($roomID)
    {
        $this->conn->execute_query("CALL GetRoomName(?, @name)", [$roomID]);
        while ($this->conn->more_results() && $this->conn->next_result()) {
            $this->conn->use_result();
        }
        $result = $this->conn->query("SELECT @name AS name");
        $row = $result->fetch_assoc();
        return $row['name'] ?? null;
    }

    public function searchAvailable($filters)
    {
        $result = $this->conn->execute_query(
            "CALL SearchAvailableRooms(?, ?, ?, ?, ?, ?, ?)",
            [
                $filters['checkin'],
                $filters['checkout'],
                $filters['adults']    ?? null,
                $filters['children']  ?? null,
                $filters['room']      ?? null,
                $filters['room_type'] ?? null,
                $filters['cartID'],
            ]
        );

        $rooms = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
            }
            $result->free();
        }
        while ($this->conn->more_results() && $this->conn->next_result()) {
            $extra = $this->conn->use_result();
            if ($extra instanceof mysqli_result) $extra->free();
        }
        return $rooms;
    }

    public function calculateTotalAmount($roomTypeName, $basePrice, $checkin, $checkout, $numAdults = 1)
    {
        $checkinDate  = new DateTime($checkin);
        $checkoutDate = new DateTime($checkout);
        if ($checkoutDate < $checkinDate) throw new Exception("Check-out date must be after check-in date.");

        $numNights   = max(1, $checkinDate->diff($checkoutDate)->days);
        $totalAmount = $basePrice * $numNights;

        if ($numNights > 3) {
            $totalAmount *= 0.85;
            $occupancy    = stripos($roomTypeName, 'Double') !== false ? 2 : 1;
            $totalGuests  = max($numAdults, $occupancy);
            $totalAmount += $basePrice * 0.10 * $totalGuests * $numNights;
        }

        return round($totalAmount * 1.12, 2);
    }

    // ─── Admin: Individual Rooms ──────────────────────────────────────────────

    public function getAllRooms(): array
    {
        $result = $this->conn->execute_query(
            "SELECT r.RoomID, r.RoomNumber, r.Status,
                    f.FloorNumber,
                    rt.RoomTypeID, rt.RoomTypeName, rt.BasePrice,
                    rt.BedCount, rt.MaxOccupancy,
                    bt.BedName
             FROM Rooms r
             JOIN Floors f    ON r.FloorID    = f.FloorID
             JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
             LEFT JOIN BedTypes bt ON rt.BedTypeID = bt.BedTypeID
             ORDER BY r.RoomNumber ASC"
        );

        $rooms = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) $rooms[] = $row;
            $result->free();
        }
        return $rooms;
    }

    public function updateRoomStatus(int $roomID, string $status): bool
    {
        if (!in_array($status, ['available', 'occupied', 'maintenance'])) {
            throw new Exception("Invalid room status.");
        }
        $result = $this->conn->execute_query(
            "UPDATE Rooms SET Status = ? WHERE RoomID = ?",
            [$status, $roomID]
        );
        if (!$result) throw new Exception("Failed to update room status: " . $this->conn->error);
        return true;
    }

    public function addRoom(int $floorID, int $roomTypeID, string $roomNumber): bool
    {
        $result = $this->conn->execute_query(
            "INSERT INTO Rooms (RoomNumber, FloorID, RoomTypeID) VALUES (?, ?, ?)",
            [$roomNumber, $floorID, $roomTypeID]
        );
        if (!$result) throw new Exception("Failed to add room: " . $this->conn->error);
        return true;
    }

    public function deleteRoom(int $roomID): bool
    {
        $result = $this->conn->execute_query(
            "DELETE FROM Rooms WHERE RoomID = ?",
            [$roomID]
        );
        if (!$result) throw new Exception("Failed to delete room: " . $this->conn->error);
        return true;
    }

    // ─── Admin: Room Types ────────────────────────────────────────────────────

    public function getAllRoomTypes(): array
    {
        $result = $this->conn->execute_query(
            "SELECT rt.RoomTypeID, rt.RoomTypeName, rt.BasePrice,
                    rt.BedCount, rt.MaxOccupancy,
                    bt.BedTypeID, bt.BedName
             FROM RoomTypes rt
             LEFT JOIN BedTypes bt ON rt.BedTypeID = bt.BedTypeID
             ORDER BY rt.BasePrice ASC"
        );

        $types = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) $types[] = $row;
            $result->free();
        }
        return $types;
    }

    public function getAllBedTypes(): array
    {
        $result = $this->conn->execute_query("SELECT * FROM BedTypes ORDER BY BedTypeID ASC");
        $beds = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) $beds[] = $row;
            $result->free();
        }
        return $beds;
    }

    public function getAllFloors(): array
    {
        $result = $this->conn->execute_query("SELECT * FROM Floors ORDER BY FloorNumber ASC");
        $floors = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) $floors[] = $row;
            $result->free();
        }
        return $floors;
    }

    public function addRoomType(string $name, float $price, int $bedTypeID, int $bedCount, int $maxOccupancy): bool
    {
        $result = $this->conn->execute_query(
            "INSERT INTO RoomTypes (RoomTypeName, BasePrice, BedTypeID, BedCount, MaxOccupancy)
             VALUES (?, ?, ?, ?, ?)",
            [$name, $price, $bedTypeID, $bedCount, $maxOccupancy]
        );
        if (!$result) throw new Exception("Failed to add room type: " . $this->conn->error);
        return true;
    }

    public function updateRoomType(int $roomTypeID, string $name, float $price, int $bedTypeID, int $bedCount, int $maxOccupancy): bool
    {
        $result = $this->conn->execute_query(
            "UPDATE RoomTypes SET RoomTypeName = ?, BasePrice = ?, BedTypeID = ?, BedCount = ?, MaxOccupancy = ?
             WHERE RoomTypeID = ?",
            [$name, $price, $bedTypeID, $bedCount, $maxOccupancy, $roomTypeID]
        );
        if (!$result) throw new Exception("Failed to update room type: " . $this->conn->error);
        return true;
    }

    public function deleteRoomType(int $roomTypeID): bool
    {
        // Check if any rooms still use this type
        $check = $this->conn->execute_query(
            "SELECT COUNT(*) AS cnt FROM Rooms WHERE RoomTypeID = ?",
            [$roomTypeID]
        );
        $row = $check->fetch_assoc();
        if ($row['cnt'] > 0) {
            throw new Exception("Cannot delete: {$row['cnt']} room(s) still use this type.");
        }

        $result = $this->conn->execute_query(
            "DELETE FROM RoomTypes WHERE RoomTypeID = ?",
            [$roomTypeID]
        );
        if (!$result) throw new Exception("Failed to delete room type: " . $this->conn->error);
        return true;
    }
}
?>