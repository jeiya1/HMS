<?php

class Payment {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createPayment($reservationID, $methodID, $amount) {

        if($amount <= 0){//Handling Negative input
            echo "Invalid payment amount.";
            return;
        }

        $result = $this->conn->execute_query(
            "INSERT INTO Payments
            (ReservationID, MethodID, Amount, PaymentStatus)
            VALUES (?, ?, ?, 'completed')",
            [$reservationID, $methodID, $amount]
        );

        if ($result) {
            echo "Payment successful!";
        } else {
            echo "Payment failed: " . $this->conn->error;
        }

    }

}
?>