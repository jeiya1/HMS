<?php
// DEBUG LINES
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// echo "<script>alert('$var');</script>";
session_start();
require_once '../../config/connect.php';
require_once '../../app/controllers/AdminController.php';

$admin = new AdminController();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$adminPath = preg_replace('#^/admin#', '', $uri);

switch ($adminPath) {
    case '/test':
        require "../../app/views/admin/ORIGINAL.view.php";
        break;
    case '/':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminDashboard();
        break;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->login();
        } else {
            if (!isset($_SESSION['admin_logged_in'])) {
                $admin->loginForm();
                exit();
            } else {
                header('Location: /admin/dashboard');
                exit();
            }
        }
        break;

    case '/logout':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->logout();
        }
        $admin->adminDashboard();
        break;

    case '/password-change':
        $admin->passwordChange();
        break;

    case '/dashboard':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminDashboard();
        break;

    case '/reservations':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminReservations();
        break;

    case '/rooms':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminRooms();
        break;

    case '/financials':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminFinancials();
        break;
    case '/calendar':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminCalendar();
        break;
    case '/logs':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminLogs();
        break;

    // ENDPOINTS
    case '/getConfirmedReservations':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $reservationModel = new Reservation($GLOBALS['conn']);
        $reservations = $reservationModel->getAllConfirmedReservations();
        header('Content-Type: application/json');
        echo json_encode($reservations);
        break;

    case '/live-reservations':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $reservationModel = new Reservation($GLOBALS['conn']);
        $reservations = $reservationModel->getLiveReservations();
        header('Content-Type: application/json');
        echo json_encode($reservations);
        break;

    default:
        echo "404 Admin Page Not Found";
        break;
}