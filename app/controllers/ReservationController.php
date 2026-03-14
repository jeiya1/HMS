<?php

require_once '../app/models/Reservation.php';
require_once '../app/models/Room.php';
require_once '../app/models/User.php';

class ReservationController {
    public function reservation() {
        require_once '../app/views/reservations/reservation.view.html';
    }

    public function submit() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = $_POST['email'];
            $checkin = $_POST['checkin'];
            $checkout = $_POST['checkout'];
            $adults = (int)$_POST['adults'];
            $children = (int)$_POST['children'];
            $roomID = (int)$_POST['roomID'];
            $paymentMethod = (int)$_POST['paymentMethod'];
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $phone = $_POST['phone'];

            if(empty($email) || empty($fname) || empty($lname) || empty($phone)){
                echo "Please fill in all required fields.";
                return;
            }

            if(empty($checkin) || empty($checkout)){
                echo "Please select check-in and check-out dates.";
                return;
            } else if (strtotime($checkin) > strtotime($checkout)) {
                echo "Check-out date must be after check-in date.";
                return;
            } else if (strtotime($checkin) < strtotime(date('Y-m-d'))) {
                echo "Check-in date cannot be in the past.";
                return;
            }

            if($adults < 1){
                echo "At least 1 adult is required.";
                return;
            }
            if ($children < 0) {
                echo "Number of children cannot be negative.";
                return;
            }

            $roomModel = new Room($GLOBALS['conn']);

            $available = $roomModel->checkRoomAvailability($roomID,$checkin,$checkout);

            if(!$available){
                echo "Room is already booked for selected dates.";
                return;
            }

            $totalAmount = $roomModel->calculateTotalAmount(
                $roomModel->getRoomPrice($roomID),
                $checkin,
                $checkout,
                $adults,
                $children
            );

            $reservationModel = new Reservation($GLOBALS['conn']);

            $userModel = new User($GLOBALS['conn']);

            $guestID = $userModel->getGuestByEmail($email)->GuestID;

            if (!$guestID) {
                $guestID = $userModel->createGuest($email, $fname, $lname, $phone);
            }

            $reservationModel->createReservation(
                $guestID,
                $checkin,
                $checkout,
                $adults,
                $children,
                $roomID,
                $paymentMethod,
                $totalAmount
            );
            // DEBUG: show input values and their data types
            echo "Debugging input values:\n";

                echo "guestID: " . $guestID . " (Type: " . gettype($guestID) . ")\n";
                echo "checkin: " . $checkin . " (Type: " . gettype($checkin) . ")\n";
                echo "checkout: " . $checkout . " (Type: " . gettype($checkout) . ")\n";
                echo "adults: " . $adults . " (Type: " . gettype($adults) . ")\n";
                echo "children: " . $children . " (Type: " . gettype($children) . ")\n";
                echo "roomID: " . $roomID . " (Type: " . gettype($roomID) . ")\n";
                echo "paymentMethod: " . $paymentMethod . " (Type: " . gettype($paymentMethod) . ")\n";
                echo "totalAmount: " . $totalAmount . " (Type: " . gettype($totalAmount) . ")\n";
        } else {
            echo "Invalid request method.";
        }
    }
}
?>