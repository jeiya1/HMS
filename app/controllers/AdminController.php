<?php
require_once '../../app/models/User.php';
require_once '../../app/models/Statistics.php';

require_once '../../app/models/Room.php';
require_once '../../app/models/Payment.php';
require_once '../../app/models/Event.php';
require_once '../../app/models/Log.php';
class AdminController
{
    // ====== ADMIN SIDE ======
    public function admin()
    {
        $logged_in = $this->getAuthState();
        if (isset($_SESSION['logged_in_admin_id'])) {
            header("Location: /admin/dashboard");
            exit();
        } else {
            header("Location: /admin/login");
            exit();
        }
    }
    public function adminDashboard()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }

        $statisticsModel = new Statistics($GLOBALS['conn']);

        $newBookings = $statisticsModel->getNewBookingsToday();
        $checkIns = $statisticsModel->getCheckInsToday();
        $checkOuts = $statisticsModel->getCheckOutsToday();
        $revenueTotal = $statisticsModel->getTotalRevenue();

        $occupiedRooms = $statisticsModel->getOccupiedRooms();
        $reservedRooms = $statisticsModel->getReservedRooms();
        $availableRooms = $statisticsModel->getAvailableRooms();
        $maintenanceRooms = $statisticsModel->getMaintenanceRooms();

        $reservationsWeek = $statisticsModel->getReservationsLast7Days();
        $reservationsMonth = $statisticsModel->getReservationsThisMonth();
        $revenue6Months = $statisticsModel->getRevenueLast6Months();
        $userAccounts6Months = $statisticsModel->getUserAccountsLast6Months();

        $paymentsByType = $statisticsModel->getPaymentsByType();
        $topRoomTypes = $statisticsModel->getTopRoomTypes();
        require_once '../../app/views/admin/dashboard.view.php';
    }
    public function adminReservations()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        require_once '../../app/views/admin/reservations.view.php';
    }
    public function adminRooms()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        $roomModel = new Room($GLOBALS['conn']);
        // $rooms = $roomModel->getAllRooms();
        require_once '../../app/views/admin/rooms.view.php';
    }
    public function loginForm()
    {
        require_once '../../app/views/admin/login.view.php';
    }
    public function payment()
    {
        $paymentModel = new Payment($GLOBALS['conn']);
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        $payments = $paymentModel->getAllPayments();
        $refunds = $paymentModel->getRefundedPayments();
        require_once '../../app/views/payment.view.php';
    }
    public function calendar()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        $eventModel = new Event();
        $events = $eventModel->getAllEvents();

        require "../../app/views/calendar/index.php";

    }
    public function activityLogs()
    {
        if (!$this->getAuthState()) {
            header("Location: /admin/login");
            exit();
        }
        $logModel = new Log($GLOBALS['conn']);
        $adminLogs = $logModel->getAdminLogs();
        $userLogs = $logModel->getUserLogs();
        require "../../app/views/logs/index.php";
    }

    // Check if user session is active
    public function getAuthState()
    {
        return isset($_SESSION['admin_logged_in']);
    }

    public function login()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                echo json_encode([
                    "success" => false,
                    "error" => "Please enter both email and password."
                ]);
                exit;
            }

            try {
                $userModel = new User($GLOBALS['conn']);
                $user = $userModel->getUserByEmail($email);
                if (!$user) {
                    echo json_encode(["success" => false, "error" => "User not found."]);
                    exit;
                }

                // Make sure the user is an admin
                $role = $userModel->getUserRole($user->UserID);

                if ($role->RoleName !== 'admin') {
                    echo json_encode(["success" => false, "error" => "Not an admin account."]);
                    exit;
                }

                // Verify password
                if (!password_verify($password, $user->PasswordHash)) {
                    echo json_encode(["success" => false, "error" => "Incorrect password."]);
                    exit;
                }

                // Set session
                $_SESSION['admin_logged_in'] = $user->UserID;
                $_SESSION['role'] = 'admin';

                echo json_encode([
                    "success" => true,
                    "message" => "Login successful!",
                    "redirect" => "/admin/dashboard"
                ]);
                exit;

            } catch (Exception $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
                exit;
            }
        }

        // fallback for GET request, show login page
        require_once '../../app/views/admin/login.view.php';
    }
}