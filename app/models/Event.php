<?php

class Event
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Returns all confirmed/pending reservations as calendar events
    public function getCalendarEvents(): array
    {
        $result = $this->conn->execute_query(
            "SELECT
                r.ReservationID,
                r.BookingToken,
                r.Status AS ReservationStatus,
                g.FirstName,
                g.LastName,
                g.Email,
                g.PhoneContact,
                rr.ReservationRoomID,
                rr.CheckInDate,
                rr.CheckOutDate,
                rr.NumAdults,
                rr.NumChildren,
                rr.Status AS RoomStatus,
                ro.RoomNumber,
                rt.RoomTypeName,
                rt.BasePrice,
                p.Amount,
                p.PaymentStatus,
                pm.MethodName
             FROM Reservations r
             JOIN Guests g              ON r.GuestID         = g.GuestID
             JOIN ReservationRooms rr   ON rr.ReservationID  = r.ReservationID
             JOIN Rooms ro              ON rr.RoomID         = ro.RoomID
             JOIN RoomTypes rt          ON ro.RoomTypeID     = rt.RoomTypeID
             LEFT JOIN Payments p       ON p.ReservationID   = r.ReservationID
             LEFT JOIN PaymentMethods pm ON p.MethodID       = pm.MethodID
             WHERE r.Status NOT IN ('cancelled')
             ORDER BY rr.CheckInDate ASC"
        );

        $events = [];
        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            $result->free();
        }
        return $events;
    }
}
?>