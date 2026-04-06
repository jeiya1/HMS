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
    //admin functions
    public function getAllPayments()
    {
    $sql = "SELECT * FROM payments WHERE status='Paid'";
    $result = $this->conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRefundedPayments()
    {
    $sql = "SELECT * FROM payments WHERE status='Refunded'";
    $result = $this->conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function refundPayment($id)
    {
    $stmt = $this->conn->prepare(
    "UPDATE payments SET status='Refunded' WHERE id=?"
    );

    $stmt->bind_param("i",$id);

    return $stmt->execute();
    }
}