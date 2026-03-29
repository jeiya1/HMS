<?php

require_once '../app/models/Reservation.php';
require_once '../app/models/Room.php';
require_once '../app/models/User.php';
require_once '../app/models/Cart.php';

class CartController
{
    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                "success" => false,
                "error" => "Invalid request method."
            ]);
            return;
        }

        $adults = (int) $_POST['adults'];
        $roomID = (int) $_POST['room'];
        $dateRange = $_POST['checkin'];

        // ---------- VALIDATION ----------
        if (empty($dateRange)) {
            echo json_encode([
                "success" => false,
                "error" => "Please select check-in and check-out dates."
            ]);
            return;
        }

        $dates = explode(" to ", $dateRange);

        if (count($dates) !== 2) {
            echo json_encode([
                "success" => false,
                "error" => "Invalid date range."
            ]);
            return;
        }

        $checkinObj = DateTime::createFromFormat('d/m/Y', trim($dates[0]));
        $checkoutObj = DateTime::createFromFormat('d/m/Y', trim($dates[1]));

        if (!$checkinObj || !$checkoutObj) {
            echo json_encode([
                "success" => false,
                "error" => "Invalid date format."
            ]);
            return;
        }

        $checkin = $checkinObj->format('Y-m-d');
        $checkout = $checkoutObj->format('Y-m-d');

        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $checkinObj->setTime(0, 0, 0);
        $checkoutObj->setTime(0, 0, 0);

        if ($checkinObj > $checkoutObj) {
            echo json_encode([
                "success" => false,
                "error" => "Check-out date must be after check-in date."
            ]);
            return;
        }

        if ($checkinObj < $today) {
            echo json_encode([
                "success" => false,
                "error" => "Check-in date cannot be in the past."
            ]);
            return;
        }

        if ($adults < 1) {
            echo json_encode([
                "success" => false,
                "error" => "Please input at least 1 guest."
            ]);
            return;
        }

        if (empty($roomID)) {
            echo json_encode([
                "success" => false,
                "error" => "Please select a room."
            ]);
            return;
        }

        // ---------- ADD TO CART ----------
        try {
            $cart = new Cart($GLOBALS['conn']);

            $cart->addToCart($roomID, $checkin, $checkout, $adults);

            echo json_encode([
                "success" => true,
                "message" => "Room added to cart!"
            ]);

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "error" => "Failed to add to cart."
            ]);
        }
    }

}
?>