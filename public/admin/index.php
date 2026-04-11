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

// ── Role helper ───────────────────────────────────────────────────────────────
// Call before any protected action. Redirects pages, returns JSON for endpoints.
function requireRole(array $allowed): void
{
    $role = $_SESSION['role'] ?? '';
    if (in_array($role, $allowed)) return;

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

    if ($isAjax) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit();
    }

    // For receptionist hitting a page they can't access, send to reservations
    $fallback = ($_SESSION['role'] ?? '') === 'receptionist'
        ? '/admin/reservations'
        : '/admin/dashboard';
    header('Location: ' . $fallback);
    exit();
}

switch ($adminPath) {

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    case '/':
    case '/dashboard':
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin', 'manager']);
        $admin->adminDashboard();
        break;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->login();
        } else {
            if (!isset($_SESSION['admin_logged_in'])) {
                $admin->loginForm();
            } else {
                // Redirect based on role after login
                $dest = ($_SESSION['role'] ?? '') === 'receptionist'
                    ? '/admin/reservations'
                    : '/admin/dashboard';
                header('Location: ' . $dest);
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
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        $admin->adminReservations();
        break;

    case '/payments':
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin', 'manager']);
        $admin->adminPayments();
        break;

    case '/rooms':
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin', 'manager']);
        $admin->adminRooms();
        break;

    case '/calendar':
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        $admin->adminCalendar();
        break;

    case '/logs':
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin']);
        $admin->adminLogs();
        break;

    case '/backup':
        if (!isset($_SESSION['admin_logged_in'])) { header('Location: /admin/login'); exit(); }
        requireRole(['admin']);
        $admin->adminBackup();
        break;

    /*
    |--------------------------------------------------------------------------
    | ROOM ENDPOINTS — admin + manager only
    |--------------------------------------------------------------------------
    */

    case '/addRoomType':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->addRoomType(trim($_POST['name']), (float)$_POST['price'], (int)$_POST['bedTypeID'], (int)$_POST['bedCount'], (int)$_POST['occupancy']);
            echo json_encode(['success' => true, 'message' => 'Room type added']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/updateRoomType':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->updateRoomType((int)$_POST['roomTypeID'], trim($_POST['name']), (float)$_POST['price'], (int)$_POST['bedTypeID'], (int)$_POST['bedCount'], (int)$_POST['occupancy']);
            echo json_encode(['success' => true, 'message' => 'Room type updated']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/deleteRoomType':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->deleteRoomType((int)$_POST['roomTypeID']);
            echo json_encode(['success' => true, 'message' => 'Room type deleted']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/addRoom':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->addRoom((int)$_POST['floorID'], (int)$_POST['roomTypeID'], trim($_POST['roomNumber']));
            echo json_encode(['success' => true, 'message' => 'Room added']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/updateRoomStatus':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->updateRoomStatus((int)$_POST['roomID'], $_POST['status']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/deleteRoom':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        try {
            $roomModel = new Room($GLOBALS['conn']);
            $roomModel->deleteRoom((int)$_POST['roomID']);
            echo json_encode(['success' => true, 'message' => 'Room deleted']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    /*
    |--------------------------------------------------------------------------
    | RESERVATION ENDPOINTS — admin + manager + receptionist
    |--------------------------------------------------------------------------
    */

    case '/getReservations':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        try {
            echo json_encode($reservationModel->getAllConfirmedReservations());
        } catch (Exception $e) { echo json_encode(['error' => $e->getMessage()]); }
        break;

    case '/getReservationDetails':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        $token = $_GET['bookingToken'] ?? null;
        if (!$token) { echo json_encode([]); exit(); }
        try {
            echo json_encode($reservationModel->getReservationWithGuest2($token));
        } catch (Exception $e) { echo json_encode(['error' => $e->getMessage()]); }
        break;

    case '/approveReservation':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        $bookingToken = $_POST['bookingToken'] ?? null;
        if (!$bookingToken) { echo json_encode(['success' => false, 'message' => 'Missing booking token']); exit(); }
        try {
            $GLOBALS['conn']->execute_query("UPDATE Reservations SET Status = 'confirmed' WHERE BookingToken = ?", [$bookingToken]);
            $GLOBALS['conn']->execute_query("UPDATE ReservationRooms SET Status = 'confirmed' WHERE ReservationID = (SELECT ReservationID FROM Reservations WHERE BookingToken = ?)", [$bookingToken]);
            $GLOBALS['conn']->execute_query("UPDATE Payments SET PaymentStatus = 'completed' WHERE ReservationID = (SELECT ReservationID FROM Reservations WHERE BookingToken = ?)", [$bookingToken]);
            echo json_encode(['success' => true, 'message' => 'Reservation approved']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/cancelReservationAdmin':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        $bookingToken = $_POST['bookingToken'] ?? null;
        if (!$bookingToken) { echo json_encode(['success' => false, 'message' => 'Missing booking token']); exit(); }
        try {
            $reservation = $reservationModel->findByToken($bookingToken);
            if (!$reservation) { echo json_encode(['success' => false, 'message' => 'Reservation not found']); exit(); }
            $payment = $reservationModel->getReservationPayment($reservation['ReservationID']);
            $reservationModel->cancelReservationGuest($bookingToken);
            if ($payment && strtolower($payment['PaymentStatus']) === 'completed') {
                $_POST['bookingCode'] = $bookingToken;
                $admin->refundPayment();
                return;
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/checkInRoom':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        $reservationRoomID = intval($_POST['reservationRoomID'] ?? 0);
        if (!$reservationRoomID) { echo json_encode(['success' => false, 'message' => 'Missing reservation room ID']); exit(); }
        try {
            $GLOBALS['conn']->execute_query("CALL CheckInRoom(?)", [$reservationRoomID]);
            echo json_encode(['success' => true, 'message' => 'Room checked in']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/checkOutRoom':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager', 'receptionist']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        $reservationRoomID = intval($_POST['reservationRoomID'] ?? 0);
        if (!$reservationRoomID) { echo json_encode(['success' => false, 'message' => 'Missing reservation room ID']); exit(); }
        try {
            $GLOBALS['conn']->execute_query("CALL CheckOutRoom(?)", [$reservationRoomID]);
            echo json_encode(['success' => true, 'message' => 'Room checked out']);
        } catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    /*
    |--------------------------------------------------------------------------
    | PAYMENT ENDPOINTS — admin + manager only
    |--------------------------------------------------------------------------
    */

    case '/getPaymentRooms':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager']);
        $paymentID = $_GET['paymentID'] ?? null;
        if (!$paymentID) { echo json_encode([]); exit(); }
        echo json_encode($reservationModel->getPaymentRooms($paymentID));
        break;

    case '/confirmPayment':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        $admin->confirmPayment();
        break;

    case '/refundPayment':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin', 'manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        $admin->refundPayment();
        break;

    /*
    |--------------------------------------------------------------------------
    | LOG ENDPOINTS — admin only
    |--------------------------------------------------------------------------
    */

    case '/getLogs':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit(); }
        requireRole(['admin']);
        try { echo json_encode((new Log($GLOBALS['conn']))->getAllLogs()); }
        catch (Exception $e) { echo json_encode(['error' => $e->getMessage()]); }
        break;

    case '/deleteLog':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        $logID = intval($_POST['logID'] ?? 0);
        if (!$logID) { echo json_encode(['success' => false, 'message' => 'Missing log ID']); exit(); }
        try { (new Log($GLOBALS['conn']))->deleteLog($logID); echo json_encode(['success' => true]); }
        catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    case '/clearLogs':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'message' => 'Method not allowed']); exit(); }
        try { (new Log($GLOBALS['conn']))->clearAllLogs(); echo json_encode(['success' => true]); }
        catch (Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
        break;

    /*
    |--------------------------------------------------------------------------
    | BACKUP ENDPOINTS — admin only
    |--------------------------------------------------------------------------
    */

    case '/runBackup':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        $admin->runBackup();
        break;

    case '/restoreBackup':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        $admin->restoreBackup();
        break;

    case '/deleteBackup':
        header('Content-Type: application/json');
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false]); exit(); }
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        $admin->deleteBackup();
        break;

    /*
    |--------------------------------------------------------------------------
    | SETTINGS — all logged in roles
    |--------------------------------------------------------------------------
    */

    case '/username-change':
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        $admin->usernameChange();
        break;

    case '/password-change':
        if (!isset($_SESSION['admin_logged_in'])) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit(); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(); }
        $admin->passwordChange();
        break;

    default:
        echo "404 Admin Page Not Found";
        break;
}