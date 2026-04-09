<?php

require_once "../app/models/User.php";
require_once "../app/models/Room.php";
require_once "../app/models/Reservation.php";
require_once "../app/models/Cart.php";
class PagesController
{

    // ====== USER SIDE ======

    // Home page
    public function home()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/home.view.php';
    }

    // Account page
    public function account()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        $userModel = new User($GLOBALS['conn']);
        $userID = $_SESSION['logged_in_user_id'];
        $userData = $userModel->getGuestDetails($userID);
        $phoneFull = $userData['PhoneContact'] ?? '';
        $countryCodes = ['+63', '+1', '+44', '+61'];

        $country_code = '+63'; // default
        $localNumber = $phoneFull;

        foreach ($countryCodes as $code) {
            if (str_starts_with($phoneFull, $code)) {
                $country_code = $code;
                $localNumber = substr($phoneFull, strlen($code));
                break;
            }
        }
        require_once '../app/views/auth/account.view.php';
    }

    // Search page
    public function search()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/rooms/search.view.php';
    }

    // View standard room page
    public function standard()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();

        // Parse check-in and check-out from GET
        $checkinStr = $_GET['checkin'] ?? '';
        $checkin = '';
        $checkout = '';
        if (!empty($checkinStr)) {
            $dates = explode(' to ', $checkinStr);
            if (count($dates) === 2) {
                $checkin = $dates[0];
                $checkout = $dates[1];
            }
        }

        $roomNumber = $_GET['room'] ?? '';
        if (!empty($roomNumber)) {
            $roomModel = new Room($GLOBALS['conn']);
            $rooms = $roomModel->getRoomInfoByRoomNumber($roomNumber);
        }

        require_once '../app/views/rooms/standard.view.php';
    }

    // View deluxe room page
    public function deluxe()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();

        // Parse check-in and check-out from GET
        $checkinStr = $_GET['checkin'] ?? '';
        $checkin = '';
        $checkout = '';
        if (!empty($checkinStr)) {
            $dates = explode(' to ', $checkinStr);
            if (count($dates) === 2) {
                $checkin = $dates[0];
                $checkout = $dates[1];
            }
        }

        $roomNumber = $_GET['room'] ?? '';
        if (!empty($roomNumber)) {
            $roomModel = new Room($GLOBALS['conn']);
            $rooms = $roomModel->getRoomInfoByRoomNumber($roomNumber);
        }

        require_once '../app/views/rooms/deluxe.view.php';
    }

    // View suite room page
    public function suite()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();

        // Parse check-in and check-out from GET
        $checkinStr = $_GET['checkin'] ?? '';
        $checkin = '';
        $checkout = '';
        if (!empty($checkinStr)) {
            $dates = explode(' to ', $checkinStr);
            if (count($dates) === 2) {
                $checkin = $dates[0];
                $checkout = $dates[1];
            }
        }

        $roomNumber = $_GET['room'] ?? '';
        if (!empty($roomNumber)) {
            $roomModel = new Room($GLOBALS['conn']);
            $rooms = $roomModel->getRoomInfoByRoomNumber($roomNumber);
        }

        require_once '../app/views/rooms/suite.view.php';
    }

    public function reservation()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/reservations/reservation.view.html';
    }

    // View registration form
    public function registration()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/auth/auth.view.php';
    }

    // View forgot password form
    public function forgotPasswordForm()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/auth/forgot_password.view.php';
    }

    // View bookings page
    public function bookings()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        if ($logged_in) {
            $reservationModel = new Reservation($GLOBALS['conn']);
            $reservations = $reservationModel->showReservations();
        }
        require_once '../app/views/reservations/bookings.view.php';
    }

    // Privacy Policy page
    public function privacy()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/static/privacy.view.php';
    }

    // Terms and Conditions page
    public function terms()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/static/terms.view.php';
    }

    // 404 Not Found page
    public function notFound()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        http_response_code(404);
        require_once '../app/views/static/404.view.php';
    }

    // Check if user session is active
    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }

    // get current cart count
    private function getCartCount()
    {
        $cartModel = new Cart($GLOBALS['conn']);
        return $cartModel->getCartAmount();
    }
}