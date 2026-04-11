<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config/connect.php';
require_once '../../app/controllers/AdminController.php';
require_once '../../app/models/Reservation.php';
require_once '../../app/models/Log.php';
require_once '../../app/models/Event.php';

$admin = new AdminController();
$reservationModel = new Reservation($GLOBALS['conn']);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$adminPath = preg_replace('#^/admin#', '', $uri);

switch ($adminPath) {

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    case '/':
    case '/dashboard':
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
            } else {
                header('Location: /admin/dashboard');
            }
        }
        break;

    case '/logout':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->logout();
        }
        header('Location: /admin/login');
        break;

    case '/reservations':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminReservations();
        break;

    case '/payments':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminPayments();
        break;

    case '/rooms':
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit();
        }
        $admin->adminRooms();
        break;

    case '/addRoomType':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->addRoomType(
                trim($_POST['name']),
                (float) $_POST['price'],
                (int) $_POST['bedTypeID'],
                (int) $_POST['bedCount'],
                (int) $_POST['occupancy']
            );
            echo json_encode(['success' => true, 'message' => 'Room type added']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case '/updateRoomType':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->updateRoomType(
                (int) $_POST['roomTypeID'],
                trim($_POST['name']),
                (float) $_POST['price'],
                (int) $_POST['bedTypeID'],
                (int) $_POST['bedCount'],
                (int) $_POST['occupancy']
            );
            echo json_encode(['success' => true, 'message' => 'Room type updated']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case '/deleteRoomType':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->deleteRoomType((int) $_POST['roomTypeID']);
            echo json_encode(['success' => true, 'message' => 'Room type deleted']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case '/addRoom':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->addRoom(
                (int) $_POST['floorID'],
                (int) $_POST['roomTypeID'],
                trim($_POST['roomNumber'])
            );
            echo json_encode(['success' => true, 'message' => 'Room added']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case '/updateRoomStatus':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->updateRoomStatus((int) $_POST['roomID'], $_POST['status']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case '/deleteRoom':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->deleteRoom((int) $_POST['roomID']);
            echo json_encode(['success' => true, 'message' => 'Room deleted']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
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


    /*
    |--------------------------------------------------------------------------
    | RESERVATION ENDPOINTS (NEW)
    |--------------------------------------------------------------------------
    */

    // Get all reservations (grouped by booking)
    case '/getReservations':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        try {
            // reuse confirmed + pending logic (you can expand later)
            $data = $reservationModel->getAllConfirmedReservations();
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;


    // Get full reservation details (like payment modal)
    case '/getReservationDetails':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        $token = $_GET['bookingToken'] ?? null;

        if (!$token) {
            echo json_encode([]);
            exit();
        }

        try {
            $details = $reservationModel->getReservationWithGuest2($token);
            echo json_encode($details);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;


    case '/approveReservation':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false]);
            exit();
        }

        $bookingToken = $_POST['bookingToken'] ?? null;

        if (!$bookingToken) {
            echo json_encode(['success' => false, 'message' => 'Missing booking token']);
            exit();
        }

        try {
            // 1. Confirm reservation
            $GLOBALS['conn']->execute_query(
                "UPDATE Reservations SET Status = 'confirmed' WHERE BookingToken = ?",
                [$bookingToken]
            );

            // 2. Confirm ALL rooms in this reservation (pending → confirmed)
            //    THIS IS THE MISSING LINE — without it RoomStatus stays 'pending'
            //    and the Check In button never appears in the modal
            $GLOBALS['conn']->execute_query(
                "UPDATE ReservationRooms SET Status = 'confirmed'
                 WHERE ReservationID = (
                     SELECT ReservationID FROM Reservations WHERE BookingToken = ?
                 )",
                [$bookingToken]
            );

            // 3. Complete payment
            $GLOBALS['conn']->execute_query(
                "UPDATE Payments SET PaymentStatus = 'completed'
                 WHERE ReservationID = (
                     SELECT ReservationID FROM Reservations WHERE BookingToken = ?
                 )",
                [$bookingToken]
            );

            echo json_encode(['success' => true, 'message' => 'Reservation approved']);
            exit;

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }


    // CANCEL RESERVATION (and refund if paid)
    case '/cancelReservationAdmin':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        $bookingToken = $_POST['bookingToken'] ?? null;

        if (!$bookingToken) {
            echo json_encode(['success' => false, 'message' => 'Missing booking token']);
            exit();
        }

        try {
            // get reservation
            $reservation = $reservationModel->findByToken($bookingToken);

            if (!$reservation) {
                echo json_encode(['success' => false, 'message' => 'Reservation not found']);
                exit();
            }

            // check payment
            $payment = $reservationModel->getReservationPayment($reservation['ReservationID']);

            // cancel reservation
            $reservationModel->cancelReservationGuest($bookingToken);

            // auto-refund if completed
            if ($payment && strtolower($payment['PaymentStatus']) === 'completed') {
                $_POST['bookingCode'] = $bookingToken;
                $admin->refundPayment();
                return;
            }

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;


    /*
    |--------------------------------------------------------------------------
    | PAYMENT (UNCHANGED - REUSED)
    |--------------------------------------------------------------------------
    */

    case '/getPaymentRooms':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        $paymentID = $_GET['paymentID'] ?? null;

        if (!$paymentID) {
            echo json_encode([]);
            exit();
        }

        $rooms = $reservationModel->getPaymentRooms($paymentID);
        echo json_encode($rooms);
        break;


    case '/confirmPayment':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $admin->confirmPayment();
        break;


    case '/refundPayment':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $admin->refundPayment();
        break;

    case '/getLogs':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        try {
            $logModel = new Log($GLOBALS['conn']);
            echo json_encode($logModel->getAllLogs());
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;


    case '/deleteLog':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        $logID = intval($_POST['logID'] ?? 0);

        if (!$logID) {
            echo json_encode(['success' => false, 'message' => 'Missing log ID']);
            exit();
        }

        try {
            $logModel = new Log($GLOBALS['conn']);
            $logModel->deleteLog($logID);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;


    case '/clearLogs':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        try {
            $logModel = new Log($GLOBALS['conn']);
            $logModel->clearAllLogs();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;


    case '/checkInRoom':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        $reservationRoomID = intval($_POST['reservationRoomID'] ?? 0);

        if (!$reservationRoomID) {
            echo json_encode(['success' => false, 'message' => 'Missing reservation room ID']);
            exit();
        }

        try {
            $GLOBALS['conn']->execute_query(
                "CALL CheckInRoom(?)",
                [$reservationRoomID]
            );
            echo json_encode(['success' => true, 'message' => 'Room checked in']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
 
    case '/username-change':
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        $admin->usernameChange();
        break;
 
    case '/password-change':
        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit();
        }
        $admin->passwordChange();
        break;

    case '/checkOutRoom':
        header('Content-Type: application/json');

        if (!isset($_SESSION['admin_logged_in'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit();
        }

        $reservationRoomID = intval($_POST['reservationRoomID'] ?? 0);

        if (!$reservationRoomID) {
            echo json_encode(['success' => false, 'message' => 'Missing reservation room ID']);
            exit();
        }

        try {
            $GLOBALS['conn']->execute_query(
                "CALL CheckOutRoom(?)",
                [$reservationRoomID]
            );
            echo json_encode(['success' => true, 'message' => 'Room checked out']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo "404 Admin Page Not Found";
        break;
}