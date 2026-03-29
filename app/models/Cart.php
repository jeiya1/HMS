<?php 

class Cart {
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function addRoomToCart($roomID, $checkin, $checkout, $adults) {
        $CartID = $_SESSION['cart_id'];

        $result = $this->conn->execute_query(
            "CALL AddRoomToCart(?, ?, ?, ?, ?)",
            [$CartID, $roomID, $checkin, $checkout, $adults]
        );

        if ($result) {
            return true;
        } else {
            throw new Exception("Failed to add to cart.");
        }
    }
}

?>