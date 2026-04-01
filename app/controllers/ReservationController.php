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

        // Extract guest info
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
        $totalAmount = trim($input['totalAmount'] ?? 0);

        if (!$fname || !$lname || !$email || !$phone || !$birthDate || !$paymentMethodID) {
            echo json_encode(["success" => false, "error" => "Incomplete guest information."]);
            return;
        }

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
            // Determine guest ID
            if (!isset($_SESSION['logged_in_user_id'])) {
                $guestIDObj = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
                $guestID = $guestIDObj->GuestID ?? $guestIDObj;
            } else {
                $currentUserGuestIDObj = $userModel->getGuestIDbyUserID($_SESSION['logged_in_user_id']);
                $currentUserEmail = $userModel->getGuestEmailByID($currentUserGuestIDObj->GuestID);

                if ($email !== $currentUserEmail) {
                    $newGuest = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
                    if (is_object($newGuest) && isset($newGuest->GuestID)) {
                        $guestID = $newGuest->GuestID;
                    } elseif (is_int($newGuest) || is_string($newGuest)) {
                        $guestID = $newGuest;
                    } else {
                        throw new Exception("Failed to create guest. GuestID is null.");
                    }
                } else {
                    $guestID = $currentUserGuestIDObj->GuestID;
                }

                // if (isset($_SESSION['logged_in_user_id'])) {
                // $reservationModel->linkReservationToUser($reservationID, $_SESSION['logged_in_user_id']);
                // }
            }

            // Fetch cart rows
            $cartRows = $cartModel->getCartRows();

            if (empty($cartRows)) {
                echo json_encode(["success" => false, "error" => "Cart is empty."]);
                return;
            }

            // Book rooms atomically
            $reservationData = $reservationModel->bookRoomsAtomic($guestID, $paymentMethodID, $totalAmount, $cartRows);

            // Link reservation to logged-in user
            if (isset($_SESSION['logged_in_user_id'])) {
                $reservationModel->linkReservationToUser($reservationData['ReservationID'], $_SESSION['logged_in_user_id']);
            }

            // Remove cart items
            $cartModel->removeCartItems($cartID);

            echo json_encode([
                "success" => true,
                "message" => "Reservation completed successfully.",
                "BookingToken" => $reservationData['BookingToken']
            ]);

        } catch (Exception $e) {
            if ($e->getMessage() === 'Room is already booked for these dates.') {
                $cartModel = new Cart($GLOBALS['conn']);
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
            'BookingToken' => $reservationData[0]['BookingToken'],
            'BookingDate' => date('F j, Y', strtotime($reservationData[0]['BookingDate'])),
        ];
        $payment = $reservationModel->getReservationPayment($reservationID); // new method
        $rooms = $reservationData;

        require __DIR__ . '/../views/reservations/reservation-details.view.php';
    }
}
?>