<?php

require_once '../app/models/Reservation.php';
require_once '../app/models/Room.php';
require_once '../app/models/User.php';

class ReservationController
{
    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["success" => false, "error" => "Invalid request method."]);
            return;
        }

        $email = $_POST['email'];
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $adults = (int) $_POST['adults'];
        $roomID = (int) $_POST['roomID'];
        $paymentMethod = (int) $_POST['paymentMethod'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $phone = $_POST['phone'];
        $birthDate = $_POST['birthDate'];

        // ---------- VALIDATION ----------
        if (empty($email) || empty($fname) || empty($lname) || empty($phone) || empty($birthDate)) {
            echo json_encode(["success" => false, "error" => "Please fill in all required fields."]);
            return;
        }

        if (empty($checkin) || empty($checkout)) {
            echo json_encode(["success" => false, "error" => "Please select check-in and check-out dates."]);
            return;
        }

        if (strtotime($checkin) > strtotime($checkout)) {
            echo json_encode(["success" => false, "error" => "Check-out must be after check-in."]);
            return;
        }

        if (strtotime($checkin) < strtotime(date('Y-m-d'))) {
            echo json_encode(["success" => false, "error" => "Check-in cannot be in the past."]);
            return;
        }

        if ($adults < 1) {
            echo json_encode(["success" => false, "error" => "At least 1 adult required."]);
            return;
        }

        // ---------- ROOM CHECK ----------
        $roomModel = new Room($GLOBALS['conn']);
        $available = $roomModel->checkRoomAvailability($roomID, $checkin, $checkout);

        if (!$available) {
            echo json_encode(["success" => false, "error" => "Room is already booked for selected dates."]);
            return;
        }

        // ---------- COMPUTE ----------
        $totalAmount = $roomModel->calculateTotalAmount(
            $roomModel->getRoomTypeName($roomID),
            $roomModel->getRoomPrice($roomID),
            $checkin,
            $checkout,
            $adults
        );

        $reservationModel = new Reservation($GLOBALS['conn']);
        $userModel = new User($GLOBALS['conn']);

        $guest = $userModel->getGuestByEmail($email);
        $guestID = $guest ? $guest->GuestID : null;

        if (!$guestID) {
            $guestID = $userModel->createGuest($email, $fname, $lname, $phone, $birthDate);
        }

        $reservations = $reservationModel->getGuestReservations($guestID);

        if ($reservations->num_rows > 0) {
            $reservationModel->addRoomToReservation(
                $reservations->fetch_object()->ReservationID,
                $roomID
            );

            echo json_encode([
                "success" => true,
                "message" => "Room added to existing reservation."
            ]);
            return;
        }

        $res = $reservationModel->createReservation(
            $guestID,
            $checkin,
            $checkout,
            $adults,
            $roomID,
            $paymentMethod,
            $totalAmount
        );

        if ($res) {
            $bookingToken = $res['BookingToken'] ?? 'N/A';

            echo json_encode([
                "success" => true,
                "message" => "Reservation created!",
                "bookingToken" => $bookingToken
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Failed to create reservation."
            ]);
        }
    }

    public function cart_submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["success" => false, "error" => "Invalid request method."]);
            return;
        }

        $roomID = (int) $_POST['roomID'];
        $guests = (int) $_POST['guests'];
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];

        if (!$roomID || !$guests || !$checkin || !$checkout) {
            echo json_encode(["success" => false, "error" => "All fields are required."]);
            return;
        }

        $reservationModel = new Reservation($GLOBALS['conn']);
        $reservationModel->addRoomToCart($roomID, $checkin, $checkout, $guests);

        echo json_encode([
            "success" => true,
            "message" => "Room added to cart!"
        ]);
    }
}
?>