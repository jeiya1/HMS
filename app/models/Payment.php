<?php

class Payment {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a payment record for a reservation
    public function createPayment($reservationID, $methodID, $amount) {

        // Validate amount is positive
        if($amount <= 0){
            throw new Exception("Invalid payment amount");
        }

        // Insert payment record with completed status
        $result = $this->conn->execute_query(
            "INSERT INTO Payments
            (ReservationID, MethodID, Amount, PaymentStatus)
            VALUES (?, ?, ?, 'completed')",
            [$reservationID, $methodID, $amount]
        );

        if (!$result) {
            throw new Exception("Failed to add to cart" . $this->conn->error);
        }

    }

}
?>