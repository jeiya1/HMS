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
        $roomID = (int) $_POST['roomID'];
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

        $checkinObj = DateTime::createFromFormat('Y/m/d', trim($dates[0]));
        $checkoutObj = DateTime::createFromFormat('Y/m/d', trim($dates[1]));

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
            exit;
        }

        if ($checkinObj < $today) {
            echo json_encode([
                "success" => false,
                "error" => "Check-in date cannot be in the past."
            ]);
            exit;
        }

        if ($adults < 1) {
            echo json_encode([
                "success" => false,
                "error" => "Please input at least 1 guest."
            ]);
            exit;
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

            $cart->addRoomToCart($roomID, $checkin, $checkout, $adults);

            $cart = new Cart($GLOBALS['conn']);
            $cartCount = $cart->getCartAmount();

            echo json_encode([
                "success" => true,
                "message" => "Room added to cart!",
                "cartCount" => $cartCount // <-- add this
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

    public function getSessionCarts()
    {
        $logged_in = $this->getAuthState();
        $cartModel = new Cart($GLOBALS['conn']);

        $carts = $cartModel->getCartRows();

        $cartCount = $cartModel->getCartAmount();

        require_once __DIR__ . '/../views/cart/cart.view.php';
    }

    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }

    public function remove()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $cartRoomID = (int) ($_POST['cartRoomID'] ?? 0);

        if (!$cartRoomID) {
            echo json_encode(['success' => false, 'error' => 'CartRoomID missing']);
            return;
        }

        try {
            $cartModel = new Cart($GLOBALS['conn']);
            $cartModel->removeCartItem($cartRoomID);

            $newCartCount = $cartModel->getCartAmount();

            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartCount' => $newCartCount
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
?>