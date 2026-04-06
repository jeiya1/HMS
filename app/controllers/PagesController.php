<?php

require_once '../app/models/Reservation.php';
require_once '../app/models/Room.php';
require_once '../app/models/Statistics.php';
require_once "../app/models/Payment.php";
require_once "../app/models/Event.php";
require_once "../app/models/Log.php";
class PagesController
{
    public function privacy()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/static/privacy.view.php';
    }

    public function admin()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        $statisticsModel = new Statistics($GLOBALS['conn']);

        // $reservationsWeek = $statisticsModel->getReservationsLast7Days();
        // $reservationsMonth = $statisticsModel->getReservationsThisMonth();
        // $revenue6Months = $statisticsModel->getRevenueLast6Months();
        // $revenueTotal = $statisticsModel->getTotalRevenue();
        // $newBookings = $statisticsModel->getNewBookingsToday();
        // $checkIns = $statisticsModel->getCheckInsToday();
        // $checkOuts = $statisticsModel->getCheckOutsToday();
        // $revenueTotal = $statisticsModel->getTotalRevenue();
        // $occupiedRooms = $statisticsModel->getOccupiedRooms();
        // $reservedRooms = $statisticsModel->getReservedRooms();
        // $availableRooms = $statisticsModel->getAvailableRooms();
        // $maintenanceRooms = $statisticsModel->getMaintenanceRooms();
        require_once '../app/views/admin/admin.view.php';
    }
    public function adminReservations()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/admin/reservations.view.php';
    }
    public function adminRooms()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        $roomModel = new Room($GLOBALS['conn']);
        $rooms = $roomModel->getAllRooms();
        require_once '../app/views/admin/rooms.view.php';
    }
    public function adminDashboard()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/admin/dashboard.view.php';
    }

    public function admin_login()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/admin/login.view.php';
    }
    public function payment()
    {
        $paymentModel = new Payment($GLOBALS['conn']);
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        $payments = $paymentModel->getAllPayments();
        $refunds = $paymentModel->getRefundedPayments();
        require_once '../app/views/payment.view.php';
    }
    public function calendar()
    {
        $eventModel = new Event();
        $events = $eventModel->getAllEvents();

        require "../app/views/calendar/index.php";

    }
    public function activityLogs()
    {
        $logModel = new Log($GLOBALS['conn']);
        $adminLogs = $logModel->getAdminLogs();
        $userLogs = $logModel->getUserLogs();
        require "../app/views/logs/index.php";
    }
    public function terms()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/static/terms.view.php';
    }
    public function home()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/home.view.php';
    }
    public function account()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/auth/account.view.php';
    }

    public function search()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/rooms/search.view.php';
    }

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

    public function registration()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/auth/auth.view.php';
    }

    public function forgotPasswordForm()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        require_once '../app/views/auth/forgot_password.view.php';
    }

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

    public function notFound()
    {
        $logged_in = $this->getAuthState();
        $cartCount = $this->getCartCount();
        http_response_code(404);
        require_once '../app/views/static/404.view.php';
    }

    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }
    private function getCartCount()
    {
        $cartModel = new Cart($GLOBALS['conn']);
        return $cartModel->getCartAmount();
    }
}