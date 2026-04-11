<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Reservation
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function areRoomsAvailable($carts)
    {
        foreach ($carts as $cart) {
            $roomID = $cart['RoomID'];
            $checkIn = $cart['CheckInDate'];
            $checkOut = $cart['CheckOutDate'];

            // Clear all result sets
            while ($this->conn->more_results()) {
                $this->conn->next_result();
            }

            $this->conn->execute_query("CALL CheckRoomAvailability(?, ?, ?, @isAvailable)", [$roomID, $checkIn, $checkOut]);

            // Fetch and free all result sets returned by the procedure
            do {
                if ($res = $this->conn->store_result()) {
                    $res->free();
                }
            } while ($this->conn->more_results() && $this->conn->next_result());

            $result = $this->conn->query("SELECT @isAvailable AS isAvailable;");
            $row = $result->fetch_assoc();
            $result->free();

            if ((int) $row['isAvailable'] === 0) {
                return [
                    'success' => false,
                    'roomID' => $roomID,
                    'message' => 'Room ' . $roomID . ' is already booked for these dates'
                ];
            }
        }

        return ['success' => true];
    }

    public function createReservation($guestID, $paymentMethod, $totalAmount)
    {
        try {
            // Call procedure to create reservation
            $this->conn->execute_query(
                "CALL CreateReservation(?, ?, ?, @ReservationID, @BookingToken)",
                [$guestID, $paymentMethod, $totalAmount]
            );

            // Fetch Reservation ID and Booking Token
            $result = $this->conn->query("SELECT @ReservationID AS ReservationID, @BookingToken AS BookingToken;");


            if ($result) {
                $row = $result->fetch_assoc();
                $result->free();
                return [
                    'ReservationID' => $row['ReservationID'],
                    'BookingToken' => $row['BookingToken']
                ];
            } else {
                throw new Exception("Failed to create reservation: " . $this->conn->error);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to create reservation: " . $e->getMessage());
        }
    }

    public function bookRoomsAtomic(
        $guestID,
        $paymentMethodID,
        $totalBeforeDiscount,
        $cartRows,
        $discountType = null,
        $discountAmount = 0,
        $discountCardNumber = null
    ) {
        $cartJSON = json_encode($cartRows);

        // Clear previous results
        while ($this->conn->more_results()) {
            $this->conn->next_result();
        }

        $this->conn->execute_query(
            "CALL BookRoomsAtomic(?, ?, ?, ?, ?, ?, ?, @ReservationID, @BookingToken, @Success, @Message)",
            [$guestID, $paymentMethodID, $totalBeforeDiscount, $cartJSON, $discountType, $discountAmount, $discountCardNumber]
        );

        // Flush any remaining results
        while ($this->conn->more_results()) {
            if ($res = $this->conn->store_result()) {
                $res->free();
            }
            $this->conn->next_result();
        }

        $result = $this->conn->query("SELECT @ReservationID AS ReservationID, @BookingToken AS BookingToken, @Success AS Success, @Message AS Message");
        $row = $result->fetch_assoc();
        $result->free();

        if (!(bool) $row['Success']) {
            $msg = empty($row['Message']) ? 'Room is already booked for these dates.' : $row['Message'];
            throw new Exception($msg);
        }

        return [
            'ReservationID' => $row['ReservationID'],
            'BookingToken' => $row['BookingToken']
        ];
    }

    public function addRoomToReservation($reservationID, $roomID, $checkIn, $checkOut, $numAdults, $numChildren = 0)
    {
        // Call procedure with OUT params
        $this->conn->execute_query(
            "CALL AddRoomToReservation(?, ?, ?, ?, ?, ?, @success, @message)",
            [$reservationID, $roomID, $checkIn, $checkOut, $numAdults, $numChildren = 0]
        );

        // Fetch OUT params
        $result = $this->conn->query("SELECT @success AS success, @message AS message;");
        if ($result) {
            $row = $result->fetch_assoc();
            $result->free();

            if (!$row['success']) {
                throw new Exception($row['message']);  // <-- THIS throws and triggers rollback
            }

            return true;
        } else {
            throw new Exception('Failed to retrieve procedure output: ' . $this->conn->error);
        }
    }

    public function sendReservationConfirmation($guestEmail, $bookingToken, $totalAmount)
    {
        // Fetch reservation details with all rooms
        $reservationDetails = $this->getReservationWithGuest($bookingToken);

        if (empty($reservationDetails)) {
            throw new Exception("Reservation not found for the provided token.");
        }

        $guestName = $reservationDetails[0]['FirstName'] . ' ' . $reservationDetails[0]['LastName'];
        $cancelUrl = "http://hms.aniagtech.com/reservation/cancel/guest/{$bookingToken}";

        // Build rooms HTML
        $roomsHtml = "";
        foreach ($reservationDetails as $room) {
            $checkIn = date('Y-m-d', strtotime($room['CheckInDate']));
            $checkOut = date('Y-m-d', strtotime($room['CheckOutDate']));
            $roomsHtml .= "
            <strong>Room:</strong> {$room['RoomType']} (#{$room['RoomNumber']})<br>
            <strong>Check-in:</strong> {$checkIn} 12:00 PM<br>
            <strong>Check-out:</strong> {$checkOut} 11:00 AM<br><br>
        ";
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($guestEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Reservation Confirmation';

            $mail->Body = "
            Dear {$guestName},<br><br>
            Your reservation has been confirmed.<br>
            <strong>Booking Token:</strong> {$bookingToken}<br><br>
            {$roomsHtml}
            <strong>Total Amount:</strong> {$totalAmount}<br><br>
            If you need to cancel your reservation, please click the link below:<br>
            <a href='{$cancelUrl}' target='_blank'>Cancel Reservation</a><br><br>
            Thank you for choosing our hotel.
        ";

            // Build plain text alternative
            $altBody = "Dear {$guestName},\n\nYour reservation has been confirmed.\nBooking Token: {$bookingToken}\n\n";
            foreach ($reservationDetails as $room) {
                $checkIn = date('Y-m-d', strtotime($room['CheckInDate']));
                $checkOut = date('Y-m-d', strtotime($room['CheckOutDate']));
                $altBody .= "Room: {$room['RoomType']} (#{$room['RoomNumber']})\nCheck-in: {$checkIn}\nCheck-out: {$checkOut}\n\n";
            }
            $altBody .= "Total Amount: {$totalAmount}\n\nCancel reservation: {$cancelUrl}\n\nThank you for choosing our hotel.";

            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to send confirmation email: " . $mail->ErrorInfo);
        }
    }
    public function cancelReservation($bookingToken, $guestID)
    {
        // Step 1: Get ReservationID (validate ownership + status)
        $result = $this->conn->execute_query("
        SELECT ReservationID 
        FROM Reservations
        WHERE BookingToken = ?
        AND GuestID = ?
        AND Status IN ('pending', 'confirmed')
        LIMIT 1
    ", [$bookingToken, $guestID]);

        $reservation = $result->fetch_assoc();

        if (!$reservation) {
            return false;
        }

        $reservationID = $reservation['ReservationID'];

        // Step 2: Call stored procedure
        $this->conn->execute_query(
            "CALL CancelReservation(?)",
            [$reservationID]
        );

        return true;
    }

    public function getGuestReservations($GuestID)
    {
        try {
            $result = $this->conn->execute_query(
                "SELECT * FROM Reservations WHERE GuestID = ?",
                [$GuestID]
            );
            return $result;
        } catch (Exception $e) {
            throw new Exception("Failed to cancel reservation: " . $e->getMessage());
        }
    }

    public function getGuestReservationByToken($bookingToken)
    {
        try {
            $result = $this->conn->execute_query(
                "SELECT * 
                FROM Reservations 
                WHERE BookingToken = ? 
                AND Status IN ('pending', 'confirmed')
                LIMIT 1",
                [$bookingToken]
            );

            $reservation = $result->fetch_assoc();

            // Return null if no reservation found
            return $reservation ?: null;

        } catch (Exception $e) {
            throw new Exception("Failed to retrieve reservation: " . $e->getMessage());
        }
    }

    public function showReservations()
    {
        // Get reservations linked to the logged-in user
        try {
            $userID = $_SESSION["logged_in_user_id"];

            $result = $this->conn->execute_query(
                "SELECT
                    r.ReservationID,
                    r.BookingToken,
                    r.Status AS ReservationStatus,
                    pm.MethodName AS PaymentMethod,
                    p.TotalBeforeDiscount,
                    p.DiscountAmount,
                    p.Amount,
                    p.PaymentDate,
                    r.CreatedAt,
                    g.FirstName AS GuestFirstName,
                    g.LastName AS GuestLastName,
                    g.Email AS GuestEmail
                FROM Reservations r
                INNER JOIN Guests g ON r.GuestID = g.GuestID
                LEFT JOIN Payments p ON r.ReservationID = p.ReservationID
                LEFT JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
                INNER JOIN UserReservations ur ON r.ReservationID = ur.ReservationID
                WHERE ur.UserID = ?",
                [$userID]
            );

            if (!$result) {
                throw new Exception("Error retrieving reservations: " . $this->conn->error);
            }

            if ($result->num_rows === 0) {
                return [];
            }

            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Error retrieving reservations: " . $e->getMessage());
        }
    }
    public function getReservationsForUser($userID)
    {
        // Get reservations linked to a specific user
        $result = $this->conn->execute_query(
            "SELECT r.* 
         FROM Reservations r
         JOIN UserReservations ur ON r.ReservationID = ur.ReservationID
         WHERE ur.UserID = ?",
            [$userID]
        );

        $reservations = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }

        return $reservations;
    }
    public function linkReservationToUser($reservationID, $userID)
    {
        $result = $this->conn->execute_query(
            "INSERT INTO UserReservations (UserID, ReservationID) VALUES (?, ?)",
            [$userID, $reservationID]
        );

        if (!$result) {
            throw new Exception("Failed to link reservation to user: " . $this->conn->error);
        }

        return true;
    }

    public function isReservationLinkedToUser($reservationID, $userID)
    {
        // Check if the reservation belongs to the user
        $result = $this->conn->execute_query(
            "SELECT 1 FROM UserReservations WHERE ReservationID = ? AND UserID = ?",
            [$reservationID, $userID]
        );

        return $result && $result->num_rows > 0;
    }

    public function findByToken($token)
    {
        // Get reservation based on token
        $result = $this->conn->execute_query(
            "SELECT * FROM Reservations WHERE BookingToken = ?",
            [$token]
        );

        return $result->fetch_assoc();
    }

    public function getReservationRooms($token)
    {
        // Get reservation rooms based on token
        $result = $this->conn->execute_query(
            "SELECT
                res.ReservationID,
                rt.RoomTypeName AS RoomType,
                r.RoomNumber,
                rr.NumAdults AS NumGuests,
                rt.BasePrice,
                rr.CheckInDate,
                rr.CheckOutDate
            FROM Reservations res
            JOIN ReservationRooms rr ON rr.ReservationID = res.ReservationID
            JOIN Rooms r ON r.RoomID = rr.RoomID
            JOIN RoomTypes rt ON rt.RoomTypeID = r.RoomTypeID
            WHERE res.BookingToken = ?",
            [$token]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function checkUserReservation($userID, $reservationID)
    {
        // Check if the reservation belongs to the user
        $result = $this->conn->execute_query(
            "SELECT 1 FROM UserReservations WHERE UserID = ? AND ReservationID = ? LIMIT 1",
            [$userID, $reservationID]
        );
        return $result && $result->num_rows > 0;
    }

    public function getReservationWithGuest($token)
    {
        // Get reservation details with guest based on token
        $result = $this->conn->execute_query(
            "SELECT
            res.ReservationID,
            res.BookingToken,
            res.CreatedAt AS BookingDate,
            g.FirstName,
            g.LastName,
            g.Email,
            g.PhoneContact,
            rr.CheckInDate,
            rr.CheckOutDate,
            rr.NumAdults,
            rr.NumChildren, -- added children
            r.RoomNumber,
            rt.RoomTypeName AS RoomType,
            rt.BasePrice
        FROM Reservations res
        JOIN Guests g ON g.GuestID = res.GuestID
        JOIN ReservationRooms rr ON rr.ReservationID = res.ReservationID
        JOIN Rooms r ON r.RoomID = rr.RoomID
        JOIN RoomTypes rt ON rt.RoomTypeID = r.RoomTypeID
        WHERE res.BookingToken = ?",
            [$token]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getReservationWithGuest2($token): array
    {
        $result = $this->conn->execute_query(
            "SELECT
                res.ReservationID,
                res.BookingToken,
                res.Status AS ReservationStatus,
                g.FirstName,
                g.LastName,
                g.Email,
                rr.ReservationRoomID,
                rr.CheckInDate,
                rr.CheckOutDate,
                rr.NumAdults,
                rr.NumChildren,
                rr.Status AS RoomStatus,
                ro.RoomNumber,
                rt.RoomTypeName AS RoomType,
                rt.BasePrice
             FROM Reservations res
             JOIN Guests g             ON res.GuestID        = g.GuestID
             JOIN ReservationRooms rr  ON rr.ReservationID   = res.ReservationID
             JOIN Rooms ro             ON rr.RoomID          = ro.RoomID
             JOIN RoomTypes rt         ON ro.RoomTypeID      = rt.RoomTypeID
             WHERE res.BookingToken = ?",
            [$token]
        );
 
        if ($result instanceof mysqli_result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
 
        return [];
    }

    public function getReservationPayment($reservationID)
    {
        // Get payment details for a reservation
        $result = $this->conn->execute_query(
            "SELECT 
            p.PaymentID, 
            pm.MethodName AS PaymentMethod, 
            p.PaymentStatus,
            p.TotalBeforeDiscount,
            p.DiscountAmount,
            p.Amount
         FROM Payments p
         JOIN PaymentMethods pm ON pm.MethodID = p.MethodID
         WHERE p.ReservationID = ? 
         LIMIT 1",
            [$reservationID]
        );

        return $result ? $result->fetch_assoc() : null;
    }

    public function cancelReservationGuest($bookingToken)
    {
        // Validate reservation exists and status allows cancellation
        $result = $this->conn->execute_query("
        SELECT ReservationID 
        FROM Reservations
        WHERE BookingToken = ?
        AND Status IN ('pending', 'confirmed')
        LIMIT 1
    ", [$bookingToken]);

        $reservation = $result->fetch_assoc();
        if (!$reservation)
            return false;

        $reservationID = $reservation['ReservationID'];

        // Call stored procedure to cancel
        $this->conn->execute_query("CALL CancelReservation(?)", [$reservationID]);

        // Flush multiple results if using mysqli
        while ($this->conn->more_results() && $this->conn->next_result()) {
        }

        return true;
    }

    public function getAllConfirmedReservations()
    {
        try {
            $result = $this->conn->execute_query(
                "SELECT 
                r.ReservationID,
                r.BookingToken,
                r.Status AS ReservationStatus,
                r.CreatedAt AS ReservationDate,
                g.FirstName AS GuestFirstName,
                g.LastName AS GuestLastName,
                g.Email AS GuestEmail,
                rr.ReservationRoomID,
                rr.CheckInDate,
                rr.CheckOutDate,
                rr.NumAdults,
                rr.NumChildren,
                rooms.RoomNumber,
                rt.RoomTypeName,
                rt.BasePrice,
                p.Amount AS PaymentAmount,
                p.PaymentStatus,
                pm.MethodName AS PaymentMethod
            FROM Reservations r
            JOIN Guests g ON r.GuestID = g.GuestID
            JOIN ReservationRooms rr ON rr.ReservationID = r.ReservationID
            JOIN Rooms rooms ON rr.RoomID = rooms.RoomID
            JOIN RoomTypes rt ON rooms.RoomTypeID = rt.RoomTypeID
            LEFT JOIN Payments p ON p.ReservationID = r.ReservationID
            LEFT JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
            WHERE r.Status IN ('pending', 'confirmed')
            ORDER BY r.CreatedAt DESC, rr.CheckInDate ASC"
            );

            $reservations = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $resID = $row['ReservationID'];
                    if (!isset($reservations[$resID])) {
                        $reservations[$resID] = [
                            'ReservationID' => $resID,
                            'BookingToken' => $row['BookingToken'],
                            'ReservationStatus' => $row['ReservationStatus'],
                            'ReservationDate' => $row['ReservationDate'],
                            'GuestFirstName' => $row['GuestFirstName'],
                            'GuestLastName' => $row['GuestLastName'],
                            'GuestEmail' => $row['GuestEmail'],
                            'PaymentAmount' => $row['PaymentAmount'],
                            'PaymentStatus' => $row['PaymentStatus'],
                            'PaymentMethod' => $row['PaymentMethod'],
                            'Rooms' => []
                        ];
                    }

                    $reservations[$resID]['Rooms'][] = [
                        'ReservationRoomID' => $row['ReservationRoomID'],
                        'RoomNumber' => $row['RoomNumber'],
                        'RoomType' => $row['RoomTypeName'],
                        'BasePrice' => $row['BasePrice'],
                        'CheckInDate' => $row['CheckInDate'],
                        'CheckOutDate' => $row['CheckOutDate'],
                        'NumAdults' => $row['NumAdults'],
                        'NumChildren' => $row['NumChildren']
                    ];
                }
            }

            return array_values($reservations);

        } catch (Exception $e) {
            throw new Exception("Failed to fetch confirmed reservations: " . $e->getMessage());
        }
    }
    public function getLiveReservations()
    {
        try {
            $result = $this->conn->execute_query(
                "SELECT 
                r.ReservationID,
                r.BookingToken,
                r.Status AS ReservationStatus,
                g.FirstName AS GuestFirstName,
                g.LastName AS GuestLastName,
                g.Email AS GuestEmail,
                rr.ReservationRoomID,
                rr.RoomID,
                rooms.RoomNumber,
                rt.RoomTypeName,
                rr.CheckInDate,
                rr.CheckOutDate,
                rr.NumAdults,
                rr.NumChildren,
                p.Amount AS PaymentAmount,
                p.PaymentStatus,
                pm.MethodName AS PaymentMethod
            FROM Reservations r
            JOIN Guests g ON r.GuestID = g.GuestID
            JOIN ReservationRooms rr ON rr.ReservationID = r.ReservationID
            JOIN Rooms rooms ON rr.RoomID = rooms.RoomID
            JOIN RoomTypes rt ON rooms.RoomTypeID = rt.RoomTypeID
            LEFT JOIN Payments p ON p.ReservationID = r.ReservationID
            LEFT JOIN PaymentMethods pm ON p.MethodID = pm.MethodID
            WHERE r.Status = 'pending' OR p.PaymentStatus = 'pending'
            ORDER BY r.CreatedAt DESC"
            );

            $reservations = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $resID = $row['ReservationID'];
                    if (!isset($reservations[$resID])) {
                        $reservations[$resID] = [
                            'ReservationID' => $resID,
                            'BookingToken' => $row['BookingToken'],
                            'ReservationStatus' => $row['ReservationStatus'],
                            'GuestFirstName' => $row['GuestFirstName'],
                            'GuestLastName' => $row['GuestLastName'],
                            'GuestEmail' => $row['GuestEmail'],
                            'PaymentAmount' => $row['PaymentAmount'],
                            'PaymentStatus' => $row['PaymentStatus'],
                            'PaymentMethod' => $row['PaymentMethod'],
                            'Rooms' => []
                        ];
                    }

                    $reservations[$resID]['Rooms'][] = [
                        'ReservationRoomID' => $row['ReservationRoomID'],
                        'RoomNumber' => $row['RoomNumber'],
                        'RoomTypeName' => $row['RoomTypeName'],
                        'CheckInDate' => $row['CheckInDate'],
                        'CheckOutDate' => $row['CheckOutDate'],
                        'NumAdults' => $row['NumAdults'],
                        'NumChildren' => $row['NumChildren']
                    ];
                }
            }

            return array_values($reservations);

        } catch (Exception $e) {
            throw new Exception("Failed to fetch live reservations: " . $e->getMessage());
        }
    }
    public function getPaymentRooms($paymentID)
    {
        return $this->conn->execute_query("
        SELECT rr.CheckInDate, rr.CheckOutDate,
               rr.NumAdults, rr.NumChildren,
               r.RoomNumber,
               rt.RoomTypeName
        FROM Payments p
        JOIN Reservations res ON p.ReservationID = res.ReservationID
        JOIN ReservationRooms rr ON res.ReservationID = rr.ReservationID
        JOIN Rooms r ON rr.RoomID = r.RoomID
        JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
        WHERE p.PaymentID = ?
    ", [$paymentID])->fetch_all(MYSQLI_ASSOC);
    }
}