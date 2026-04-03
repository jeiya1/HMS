<?php

require_once '../app/models/User.php';
require_once '../app/models/Cart.php';
require_once '../app/models/Reservation.php';
require_once '../app/models/ReservationCart.php';

class ReservationController
{
    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["success" => false, "error" => "Invalid request method."]);
            return;
        }

        // Retrieve reservation and payment details
        $input = json_decode(file_get_contents('php://input'), true);

        $totalBeforeDiscount = floatval($input['totalBeforeDiscount'] ?? 0);
        $discountAmount = floatval($input['discountAmount'] ?? 0);

        $guest = $input['guest'] ?? [];
        $paymentMethod = $input['paymentMethod'] ?? '';

        $paymentMap = ['Cash' => 1, 'Card' => 2, 'E-Wallet' => 3, 'Bank' => 4];
        $paymentMethodID = $paymentMap[$paymentMethod] ?? null;

        $email = trim($guest['email'] ?? '');
        $fname = trim($guest['fname'] ?? '');
        $lname = trim($guest['lname'] ?? '');
        $country = trim($guest['country_code'] ?? '');
        $phoneNo = trim($guest['phone'] ?? '');
        $phone = $country . $phoneNo;
        $birthDate = trim($guest['birthDate'] ?? '');

        $discountType = $input['discountType'] ?? null;
        $discountCardNumber = $input['discountCardNumber'] ?? null;

        // Validate guest info
        if (!$fname || !$lname || !$email || !$phone || !$birthDate || !$paymentMethodID) {
            echo json_encode(["success" => false, "error" => "Incomplete guest information."]);
            return;
        }

        // Check for active cart session
        $cartID = $_SESSION['cart_id'] ?? null;
        $sessionGuestID = $_SESSION['session_guest_id'] ?? null;

        if (!$cartID || !$sessionGuestID) {
            echo json_encode(["success" => false, "error" => "No active cart found."]);
            return;
        }

        $userModel = new User($GLOBALS['conn']);
        $cartModel = new Cart($GLOBALS['conn']);
        $reservationModel = new Reservation($GLOBALS['conn']);

        try {

            // =========================
            // Resolve GuestID
            // =========================
            if (!isset($_SESSION['logged_in_user_id'])) {
                $guestIDObj = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
                $guestID = is_object($guestIDObj) ? $guestIDObj->GuestID : $guestIDObj;
            } else {
                $currentUserGuestIDObj = $userModel->getGuestIDbyUserID($_SESSION['logged_in_user_id']);
                $currentUserEmail = $userModel->getGuestEmailByID($currentUserGuestIDObj->GuestID);

                if ($email !== $currentUserEmail) {
                    $newGuest = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
                    $guestID = is_object($newGuest) ? $newGuest->GuestID : $newGuest;
                } else {
                    $guestID = $currentUserGuestIDObj->GuestID;
                }
            }

            // =========================
            // Fetch Cart (IMPORTANT: must include NumChildren)
            // =========================
            $cartRows = $cartModel->getCartRows();

            if (empty($cartRows)) {
                echo json_encode(["success" => false, "error" => "Cart is empty."]);
                return;
            }

            // Ensure children field exists (defensive)
            foreach ($cartRows as &$row) {
                if (!isset($row['NumChildren'])) {
                    $row['NumChildren'] = 0;
                }
            }

            // =========================
            // Book
            // =========================
            $reservationData = $reservationModel->bookRoomsAtomic(
                $guestID,
                $paymentMethodID,
                $totalBeforeDiscount,
                $cartRows,
                $discountType,
                $discountAmount,
                $discountCardNumber
            );

            // =========================
            // Link user
            // =========================
            if (isset($_SESSION['logged_in_user_id'])) {
                $reservationModel->linkReservationToUser(
                    $reservationData['ReservationID'],
                    $_SESSION['logged_in_user_id']
                );
            }

            // =========================
            // Clear cart
            // =========================
            $cartModel->removeCartItems();

            // =========================
            // Email
            // =========================
            $confirmationEmail = isset($_SESSION['logged_in_user_id'])
                ? $userModel->getGuestEmailByID($guestID)
                : $email;

            $reservationModel->sendReservationConfirmation(
                $confirmationEmail,
                $reservationData['BookingToken'],
                number_format(($totalBeforeDiscount - $discountAmount) * 1.12, 2, '.', ',')
            );

            echo json_encode([
                "success" => true,
                "message" => "Reservation completed successfully.",
                "BookingToken" => $reservationData['BookingToken']
            ]);

        } catch (Exception $e) {

            if ($e->getMessage() === 'Room is already booked for these dates.') {
                $removedItems = $cartModel->removeUnavailableCartItems();

                echo json_encode([
                    "success" => false,
                    "error" => "Some rooms in your cart are no longer available.",
                    "removedItems" => $removedItems
                ]);
                return;
            }

            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public function show($bookingToken)
    {
        // Fetch reservation data
        $reservationModel = new Reservation($GLOBALS["conn"]);
        $reservationData = $reservationModel->getReservationWithGuest($bookingToken);

        if (empty($reservationData)) {
            require_once __DIR__ . '/../views/static/404.view.php';
            return;
        }

        $reservationID = $reservationData[0]['ReservationID'];
        $userID = $_SESSION["logged_in_user_id"] ?? null;

        // Check if user owns this reservation
        $ownsReservation = $reservationModel->checkUserReservation($userID, $reservationID);

        if (!$ownsReservation) {
            require_once __DIR__ . '/../views/static/404.view.php';
            return;
        }

        // Guest details
        $guestDetails = [
            'FullName' => $reservationData[0]['FirstName'] . ' ' . $reservationData[0]['LastName'],
            'Email' => $reservationData[0]['Email'],
            'PhoneContact' => $reservationData[0]['PhoneContact'],
            'BookingToken' => $reservationData[0]['BookingToken'],
            'BookingDate' => date('F j, Y', strtotime($reservationData[0]['BookingDate'])),
        ];

        // Get payment info
        $payment = $reservationModel->getReservationPayment($reservationID);

        // Fetch rooms with status directly from ReservationRooms.Status
        $roomsResult = $GLOBALS["conn"]->execute_query("
        SELECT rr.ReservationRoomID, rr.CheckInDate, rr.CheckOutDate, rr.NumAdults, rr.NumChildren,
               r.RoomNumber, rt.RoomTypeName AS RoomType, rt.BasePrice,
               rr.Status AS RoomStatus
        FROM ReservationRooms rr
        JOIN Rooms r ON rr.RoomID = r.RoomID
        JOIN RoomTypes rt ON r.RoomTypeID = rt.RoomTypeID
        WHERE rr.ReservationID = ?
    ", [$reservationID]);

        $rooms = $roomsResult ? $roomsResult->fetch_all(MYSQLI_ASSOC) : [];

        require __DIR__ . '/../views/reservations/reservation-details.view.php';
    }

    public function cancel()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                "success" => false,
                "error" => "Invalid request method."
            ]);
            exit;
        }

        // Check if user is logged in
        if (!isset($_SESSION['logged_in_user_id'])) {
            echo json_encode([
                "success" => false,
                "error" => "Unauthorized. Please log in."
            ]);
            exit;
        }

        $bookingToken = $_POST['booking_token'] ?? null;

        // Check for booking token
        if (!$bookingToken) {
            echo json_encode([
                "success" => false,
                "error" => "Missing booking token."
            ]);
            exit;
        }

        // Check if reservation belongs to user and cancel
        try {
            $reservationModel = new Reservation($GLOBALS['conn']);
            $userModel = new User($GLOBALS['conn']);

            $currentUserGuestIDObj = $userModel->getGuestIDbyUserID($_SESSION['logged_in_user_id']);
            $reservationModel->cancelReservation($bookingToken, $currentUserGuestIDObj->GuestID);

            echo json_encode([
                "success" => true,
                "message" => "Reservation cancelled successfully."
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
            exit;
        }
    }
    public function cancelGuest()
    {
        header('Content-Type: application/json');

        $bookingToken = $_POST['booking_token'] ?? null;

        // Check for booking token
        if (!$bookingToken) {
            echo json_encode([
                'success' => false,
                'error' => 'Missing booking token.'
            ]);
            exit;
        }

        // Check if reservation exist and is cancellable
        $reservationModel = new Reservation($GLOBALS['conn']);
        $reservation = $reservationModel->getGuestReservationByToken($bookingToken);

        if (!$reservation) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid or non-cancellable reservation.'
            ]);
            exit;
        }

        try {
            $reservationModel->cancelReservationGuest($bookingToken);

            echo json_encode([
                'success' => true,
                'message' => 'Reservation cancelled successfully.'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Guest cancellation form
    public function showGuestCancelForm($bookingToken)
    {
        $reservationModel = new Reservation($GLOBALS['conn']);
        $reservation = $reservationModel->getGuestReservationByToken($bookingToken);

        if (!$reservation) {
            header('Location: /bookings');
            exit;
        }

        // pass $reservation array to the view
        require_once '../app/views/reservations/guest-cancel.view.php';
    }
}
?>