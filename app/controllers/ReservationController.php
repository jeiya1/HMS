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

        $input = json_decode(file_get_contents('php://input'), true);

        $guest = $input['guest'] ?? [];
        $paymentMethod = $input['paymentMethod'] ?? '';

        $paymentMap = [
            'Cash' => 1,
            'Card' => 2,
            'E-Wallet' => 3,
            'Bank' => 4
        ];

        $paymentMethodID = $paymentMap[$paymentMethod] ?? null;

        $email = trim($guest['email'] ?? '');
        $fname = trim($guest['fname'] ?? '');
        $lname = trim($guest['lname'] ?? '');
        $country = trim($guest['country_code'] ?? '');
        $phoneNo = trim($guest['phone'] ?? '');
        $phone = $country . $phoneNo;
        $birthDate = trim($guest['birthDate'] ?? '');
        $totalAmount = trim($input['totalAmount']);

        if (!$fname || !$lname || !$email || !$phone || !$birthDate || !$paymentMethod) {
            echo json_encode([
                "success" => false,
                "error" => "Incomplete guest information."
            ]);
            return;
        }

        $cartID = $_SESSION['cart_id'] ?? null;
        $sessionGuestID = $_SESSION['session_guest_id'] ?? null;

        if (!$cartID || !$sessionGuestID) {
            echo json_encode([
                "success" => false,
                "error" => "No active cart found."
            ]);
            return;
        }

        $userModel = new User($GLOBALS['conn']);
        $cartModel = new Cart($GLOBALS['conn']);
        $reservationModel = new Reservation($GLOBALS['conn']);

        try {
            if (!$_SESSION['logged_in_user_id']) {
                $guestIDObj = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
                $guestID = $guestIDObj->GuestID ?? $guestIDObj;
            } else {
                $currentUserGuestIDObj = $userModel->getGuestIDbyUserID($_SESSION['logged_in_user_id']);
                $currentUserEmail = $userModel->getGuestEmailByID($currentUserGuestIDObj->GuestID);

                if ($email !== $currentUserEmail) {
                    // booking for someone else
                    $guestIDObj = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
                    $guestID = $guestIDObj->GuestID ?? $guestIDObj;
                } else {
                    $guestID = $currentUserGuestIDObj->GuestID;
                }
            }
            $reservationData = $reservationModel->createReservation($guestID, $paymentMethodID, $totalAmount);

            $reservationID = $reservationData['ReservationID'] ?? null;
            $bookingToken = $reservationData['BookingToken'] ?? null;
            // optionally link to user
            $reservationModel->linkReservationToUser($reservationID, $_SESSION['logged_in_user_id']);
            if (!$reservationID || !$bookingToken) {
                echo json_encode([
                    'success' => false,
                    'error' => "Reservation could not be made."
                ]);
            } else {
                $carts = $cartModel->getCartRows();

                foreach ($carts as $cart) {
                    $roomID = $cart['RoomID'];
                    $checkIn = $cart['CheckInDate'];
                    $checkOut = $cart['CheckOutDate'];
                    $numAdults = $cart['NumAdults'];

                    $result = $reservationModel->addRoomToReservation(
                        $reservationID,
                        $roomID,
                        $checkIn,
                        $checkOut,
                        $numAdults
                    );

                    if (!$result) {
                        echo json_encode([
                            "success" => false,
                            "error" => "Failed to add room to reservation."
                        ]);
                        return;
                    }
                }

                $cartsClear = $cartModel->removeCartItems($cartID);

                if (!$cartsClear) {
                    echo json_encode(["success" => false, "error" => "Failed to clear cart."]);
                    return;
                }

                echo json_encode([
                    "success" => true,
                    "message" => "Reservation completed successfully."
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => "Reservation failed: " . $e->getMessage()
            ]);
        }
    }

    public function show($bookingToken)
    {
        $reservationModel = new Reservation($GLOBALS["conn"]);
        $reservationData = $reservationModel->getReservationWithGuest($bookingToken);

        if (empty($reservationData)) {
            require_once __DIR__ . '/../views/static/404.view.php';
            return;
        }

        $reservationID = $reservationData[0]['ReservationID'];
        $userID = $_SESSION["logged_in_user_id"] ?? null;

        $ownsReservation = $reservationModel->checkUserReservation($userID, $reservationID);

        if (!$ownsReservation) {
            require_once __DIR__ . '/../views/static/404.view.php';
            return;
        }

        // Extract guest info from first row
        $guestDetails = [
            'FullName' => $reservationData[0]['FirstName'] . ' ' . $reservationData[0]['LastName'],
            'Email' => $reservationData[0]['Email'],
            'PhoneContact' => $reservationData[0]['PhoneContact'],
            'BookingToken'=> $reservationData[0]['BookingToken'],
            'BookingDate'=> date('j-F-Y', strtotime($reservationData[0]['BookingDate'])),
        ];
        $payment = $reservationModel->getReservationPayment($reservationID); // new method
        $rooms = $reservationData;

        require __DIR__ . '/../views/reservations/reservation-details.view.php';
    }
}
?>