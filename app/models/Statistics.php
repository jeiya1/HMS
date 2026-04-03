<?php
class Statistics {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Reservations last 7 days
    public function getReservationsLast7Days() {
        $result = $this->conn->execute_query("
            SELECT DAYNAME(rr.CheckInDate) AS day, 
                   SUM(CASE WHEN rr.Status='confirmed' THEN 1 ELSE 0 END) AS booked,
                   SUM(CASE WHEN rr.Status='cancelled' THEN 1 ELSE 0 END) AS canceled
            FROM ReservationRooms rr
            WHERE rr.CheckInDate >= CURDATE() - INTERVAL 7 DAY
            GROUP BY DAYNAME(rr.CheckInDate)
            ORDER BY FIELD(DAYNAME(rr.CheckInDate),'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Reservations this month per week
    public function getReservationsThisMonth() {
        $result = $this->conn->execute_query("
            SELECT WEEK(rr.CheckInDate,1) AS week_number,
                   SUM(CASE WHEN rr.Status='confirmed' THEN 1 ELSE 0 END) AS booked,
                   SUM(CASE WHEN rr.Status='cancelled' THEN 1 ELSE 0 END) AS canceled
            FROM ReservationRooms rr
            WHERE MONTH(rr.CheckInDate) = MONTH(CURDATE())
              AND YEAR(rr.CheckInDate) = YEAR(CURDATE())
            GROUP BY WEEK(rr.CheckInDate,1)
            ORDER BY week_number;
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Revenue last 6 months
    public function getRevenueLast6Months() {
        $result = $this->conn->execute_query("
            SELECT DATE_FORMAT(PaymentDate,'%b') AS month,
                   SUM(Amount) AS revenue
            FROM Payments
            WHERE PaymentStatus = 'completed'
              AND PaymentDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY YEAR(PaymentDate), MONTH(PaymentDate)
            ORDER BY YEAR(PaymentDate), MONTH(PaymentDate);
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>