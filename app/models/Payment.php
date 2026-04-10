<?php

class Payment
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create a payment record for a reservation
    public function createPayment($reservationID, $methodID, $amount)
    {

        // Validate amount is positive
        if ($amount <= 0) {
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
    public function getAllPaymentsAdmin()
    {
        $result = $this->conn->execute_query("SELECT 
                p.PaymentID,
                r.BookingToken,
                g.FirstName,
                g.LastName,
                pm.MethodName,
                p.TotalBeforeDiscount,
                p.DiscountAmount,
                p.Amount,
                p.PaymentStatus,
                p.TransactionReference,
                p.PaymentDate,
                GROUP_CONCAT(DISTINCT ro.RoomNumber ORDER BY ro.RoomNumber ASC) AS Rooms,
                SUM(rr.NumAdults + rr.NumChildren) AS TotalGuests,
                MIN(rr.CheckInDate) AS CheckInDate,
                MAX(rr.CheckOutDate) AS CheckOutDate
            FROM Payments p
            INNER JOIN Reservations r ON p.ReservationID = r.ReservationID
            INNER JOIN Guests g ON r.GuestID = g.GuestID
            INNER JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
            INNER JOIN ReservationRooms rr ON rr.ReservationID = r.ReservationID
            INNER JOIN Rooms ro ON rr.RoomID = ro.RoomID
            GROUP BY p.PaymentID, r.BookingToken, g.FirstName, g.LastName, pm.MethodName, p.TotalBeforeDiscount, p.DiscountAmount, p.Amount, p.PaymentStatus, p.TransactionReference, p.PaymentDate
            ORDER BY p.PaymentDate DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRefundedPayments()
    {
        $sql = "SELECT * FROM payments WHERE status='Refunded'";
        $result = $this->conn->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function refundPaymentByToken($bookingToken)
    {
        try {
            $this->conn->execute_query(
                "CALL RefundPaymentByToken(?, ?)",
                [$bookingToken, null]
            );

            return true;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public function confirmPaymentByToken($bookingToken)
    {
        $this->conn->execute_query(
            "CALL CompletePaymentByToken(?, ?)",
            [$bookingToken, null]
        );

        return true;
    }
}