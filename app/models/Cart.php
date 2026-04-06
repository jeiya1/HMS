<?php

class Cart
{
    private $conn;

    // Constructor to initialize database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function addRoomToCart($roomNumber, $checkin, $checkout, $adults, $children)
    {
        // Retrieve cart ID from session
        $CartID = $_SESSION['cart_id'];

        // Get RoomID from RoomNumber
        $stmt = $this->conn->execute_query(
            "SELECT RoomID FROM Rooms WHERE RoomNumber = ?",
            [$roomNumber]
        );

        $row = $stmt->fetch_assoc();

        if (!$row) {
            throw new Exception("Room not found.");
        }

        $roomID = $row['RoomID'];

        // Check for overlapping dates in the cart
        $stmt = $this->conn->execute_query(
            "SELECT 1 
         FROM CartRooms 
         WHERE CartID = ? AND RoomID = ? 
           AND NOT (CheckOutDate <= ? OR CheckInDate >= ?)",
            [$CartID, $roomID, $checkin, $checkout]
        );

        if ($stmt->num_rows > 0) {
            throw new Exception("This room is already in your cart for overlapping dates.");
        }

        // Call stored procedure (UPDATED PARAMS)
        try {
            // Add room to cart via stored procedure
            $this->conn->execute_query(
                "CALL AddRoomToCart(?, ?, ?, ?, ?, ?)",
                [$CartID, $roomID, $checkin, $checkout, $adults, $children]
            );
            return true;
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Failed to add to cart: " . $e->getMessage());
        }
    }

    public function getCartAmount()
    {
        $CartID = $_SESSION["cart_id"] ?? null;

        if (!$CartID) {
            return 0;
        }

        // Count total items in cart
        $result = $this->conn->execute_query(
            "SELECT COUNT(*) as total FROM CartRooms WHERE CartID = ?",
            [$CartID]
        );

        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getCartRows()
    {
        $CartID = $_SESSION["cart_id"];

        if (!$CartID) {
            return [];
        }

        // Get cart items details
        $result = $this->conn->execute_query(
            "SELECT 
            cr.CartRoomID,
            r.RoomID,
            r.RoomNumber,
            rt.RoomTypeName,
            rt.BasePrice,
            cr.NumAdults,
            cr.NumChildren, -- IMPORTANT
            cr.CheckInDate,
            cr.CheckOutDate
        FROM CartRooms cr
        INNER JOIN Rooms r ON cr.RoomID = r.RoomID
        INNER JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
        WHERE cr.CartID = ?",
            [$CartID]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function removeCartItem(int $cartRoomID)
    {
        $CartID = $_SESSION['cart_id'] ?? null;
        if (!$CartID) {
            throw new Exception("No cart found for session.");
        }

        try { 
            // delete specific cart room item
            $this->conn->execute_query(
                "DELETE FROM CartRooms WHERE CartRoomID = ? AND CartID = ?",
                [$cartRoomID, $CartID]
            );
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to remove cart item: " . $e->getMessage());
        }
    }

    public function removeUnavailableCartItems()
    {
        $cartRows = $this->getCartRows();
        $removedItems = [];

        // Check each item's availability
        foreach ($cartRows as $row) {
            $roomID = $row['RoomID'];
            $checkIn = $row['CheckInDate'];
            $checkOut = $row['CheckOutDate'];

            // Call stored procedure
            $this->conn->execute_query("CALL CheckRoomAvailability(?, ?, ?, @isAvailable)", [$roomID, $checkIn, $checkOut]);

            // Fetch the output variable
            $res = $this->conn->execute_query("SELECT @isAvailable AS isAvailable");
            $availabilityRow = $res->fetch_assoc();
            $res->free();

            if (!$availabilityRow['isAvailable']) {
                // Remove the cart item
                $this->conn->execute_query("DELETE FROM CartRooms WHERE CartRoomID = ?", [$row['CartRoomID']]);
                $removedItems[] = $row;
            }
        }

        return $removedItems; // return removed items for frontend display
    }

    public function removeCartItems()
    {
        $CartID = $_SESSION['cart_id'] ?? null;

        if (!$CartID) {
            throw new Exception("No cart found for session.");
        }

        try {
            // Delete all items from cart
            $this->conn->execute_query(
                "DELETE FROM CartRooms WHERE CartID = ?",
                [$CartID]
            );
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to clear cart: " . $e->getMessage());
        }
    }
}

?>