<?php
class Statistics
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ── Dashboard stat cards ──────────────────────────────────

    // All-time total bookings
    public function getNewBookingsToday()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(*) AS total
            FROM Reservations
            WHERE Status IN ('pending', 'confirmed')
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // All-time total check-ins (rooms that have been or are checked in)
    public function getCheckInsToday()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(*) AS total
            FROM ReservationRooms
            WHERE Status IN ('checked_in', 'checked_out')
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // All-time total check-outs
    public function getCheckOutsToday()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(*) AS total
            FROM ReservationRooms
            WHERE Status = 'checked_out'
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // ── Room availability (as of TODAY) ───────────────────────

    // Rooms physically occupied right now
    public function getOccupiedRooms()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(*) AS total
            FROM Rooms
            WHERE Status = 'occupied'
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Rooms with a confirmed/pending reservation overlapping today
    // (booked for today but not yet checked in)
    public function getReservedRooms()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(DISTINCT rr.RoomID) AS total
            FROM ReservationRooms rr
            JOIN Reservations r ON rr.ReservationID = r.ReservationID
            WHERE r.Status IN ('pending', 'confirmed')
              AND rr.Status IN ('pending', 'confirmed')
              AND rr.CheckInDate <= CURDATE()
              AND rr.CheckOutDate > CURDATE()
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Rooms available for today
    // (Status = available AND not booked for today)
    public function getAvailableRooms()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(*) AS total
            FROM Rooms r
            WHERE r.Status = 'available'
              AND r.RoomID NOT IN (
                  SELECT rr.RoomID
                  FROM ReservationRooms rr
                  JOIN Reservations res ON rr.ReservationID = res.ReservationID
                  WHERE res.Status IN ('pending', 'confirmed')
                    AND rr.Status IN ('pending', 'confirmed')
                    AND rr.CheckInDate <= CURDATE()
                    AND rr.CheckOutDate > CURDATE()
              )
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Rooms under maintenance
    public function getMaintenanceRooms()
    {
        $result = $this->conn->execute_query("
            SELECT COUNT(*) AS total
            FROM Rooms
            WHERE Status = 'maintenance'
        ");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // ── Charts ────────────────────────────────────────────────

    // Reservations last 7 days
    public function getReservationsLast7Days()
    {
        $result = $this->conn->execute_query("
            SELECT DAYNAME(rr.CheckInDate) AS day,
                   SUM(CASE WHEN rr.Status = 'confirmed'  THEN 1 ELSE 0 END) AS booked,
                   SUM(CASE WHEN rr.Status = 'cancelled'  THEN 1 ELSE 0 END) AS canceled
            FROM ReservationRooms rr
            WHERE rr.CheckInDate >= CURDATE() - INTERVAL 7 DAY
            GROUP BY DAYNAME(rr.CheckInDate)
            ORDER BY FIELD(DAYNAME(rr.CheckInDate),
                'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Reservations this month per week
    public function getReservationsThisMonth()
    {
        $result = $this->conn->execute_query("
            SELECT WEEK(rr.CheckInDate, 1) AS week_number,
                   SUM(CASE WHEN rr.Status = 'confirmed' THEN 1 ELSE 0 END) AS booked,
                   SUM(CASE WHEN rr.Status = 'cancelled' THEN 1 ELSE 0 END) AS canceled
            FROM ReservationRooms rr
            WHERE MONTH(rr.CheckInDate) = MONTH(CURDATE())
              AND YEAR(rr.CheckInDate)  = YEAR(CURDATE())
            GROUP BY WEEK(rr.CheckInDate, 1)
            ORDER BY week_number
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Revenue last 6 months
    public function getRevenueLast6Months()
    {
        $result = $this->conn->execute_query("
            SELECT DATE_FORMAT(PaymentDate, '%b') AS month,
                   SUM(Amount) AS revenue
            FROM Payments
            WHERE PaymentStatus = 'completed'
              AND PaymentDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY YEAR(PaymentDate), MONTH(PaymentDate)
            ORDER BY YEAR(PaymentDate), MONTH(PaymentDate)
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Total revenue all time
    public function getTotalRevenue()
    {
        $result = $this->conn->execute_query("
            SELECT COALESCE(SUM(Amount), 0) AS revenue
            FROM Payments
            WHERE PaymentStatus = 'completed'
        ");
        return $result->fetch_assoc()['revenue'] ?? 0;
    }

    // Total revenue this month
    public function getRevenueMonth()
    {
        $result = $this->conn->execute_query("
            SELECT COALESCE(SUM(Amount), 0) AS revenue
            FROM Payments
            WHERE PaymentStatus = 'completed'
              AND MONTH(PaymentDate) = MONTH(CURDATE())
              AND YEAR(PaymentDate)  = YEAR(CURDATE())
        ");
        return $result->fetch_assoc()['revenue'] ?? 0;
    }

    // Payments by method
    public function getPaymentsByType()
    {
        $result = $this->conn->execute_query("
            SELECT pm.MethodName, COUNT(p.PaymentID) AS Total
            FROM Payments p
            JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
            WHERE p.PaymentStatus = 'completed'
            GROUP BY pm.MethodName
            ORDER BY Total DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Top performing room types by bookings
    public function getTopRoomTypes()
    {
        $result = $this->conn->execute_query("
            SELECT rt.RoomTypeName,
                   COUNT(rr.ReservationRoomID) AS total
            FROM ReservationRooms rr
            JOIN Rooms r    ON rr.RoomID      = r.RoomID
            JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
            WHERE rr.Status IN ('confirmed', 'checked_in', 'checked_out')
            GROUP BY rt.RoomTypeID
            ORDER BY total DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // User accounts created per month (last 6 months)
    public function getUserAccountsLast6Months()
    {
        $result = $this->conn->execute_query("
            SELECT DATE_FORMAT(CreatedAt, '%b') AS month,
                   COUNT(*) AS accounts
            FROM Users
            WHERE CreatedAt >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY YEAR(CreatedAt), MONTH(CreatedAt)
            ORDER BY YEAR(CreatedAt), MONTH(CreatedAt)
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>