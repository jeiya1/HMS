<?php

class Payment {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createPayment($reservationID, $methodID, $amount) {

        if($amount <= 0){
            throw new Exception("Invalid payment amount");
        }

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