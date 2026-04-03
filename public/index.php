<?php
// DEBUG LINES
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// echo "<script>alert('$var');</script>";

$lifetime = 60 * 60 * 24 * 30; // 30 days

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once '../config/connect.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/PagesController.php';
require_once '../app/controllers/ReservationController.php';
require_once '../app/controllers/SearchController.php';
require_once '../app/controllers/CartController.php';
require_once '../app/models/SessionGuest.php';
require_once '../app/models/ReservationCart.php';

if (!isset($_SESSION['session_token'])) {
    $_SESSION['session_token'] = bin2hex(random_bytes(16));
}

$sessionToken = $_SESSION['session_token'];

$sessionGuestModel = new SessionGuest($GLOBALS['conn']);
$sessionGuestID = $sessionGuestModel->findOrCreate($sessionToken);

$cartModel = new ReservationCart($GLOBALS['conn']);
$cartID = $cartModel->findOrCreateCart($sessionToken);

$_SESSION['session_guest_id'] = $sessionGuestID;
$_SESSION['cart_id'] = $cartID;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$cart = new CartController();
$auth = new AuthController();
$pages = new PagesController();
$reservation = new ReservationController();
$search = new SearchController();

switch ($uri) {
    case '/':
        $pages->home();
        break;

    case '/home':
        $pages->home();
        break;

    case '/forgot-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->resetPasswordForm(); // handles sending reset email
        } else {
            $pages->forgotPasswordForm(); // shows the form
        }
        break;

    case '/reset-submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->resetPassword(); // update password in DB
        } else {
            $auth->showResetForm($_GET['token'] ?? null); // show form with token
        }
        break;

    case '/registration':
        $pages->registration();
        break;

    case '/login-submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login();
        } else {
            $pages->registration();
        }
        break;

    case '/signup-submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->signup();
        } else {
            $pages->registration();
        }
        break;

    case '/profile':
        $pages->account();
        break;

    case '/update-submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->updateProfile();
        } else {
            $pages->account();
        }
        break;

    case '/get-profile':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $pages->account();
            exit;
        }
        $auth->getProfile();
        break;

    case '/logout':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $pages->account();
            exit;
        }
        $auth->logout();
        break;

    case '/privacy':
        $pages->privacy();
        break;

    case '/terms':
        $pages->terms();
        break;

    case '/search':
        $search->search();
        break;

    case '/room/standard':
        $pages->standard();
        break;

    case '/room/deluxe':
        $pages->deluxe();
        break;

    case '/room/suite':
        $pages->suite();
        break;

    case '/cart':
        $cart->getSessionCarts();
        break;

    case '/cart-remove':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $cart->getSessionCarts();
            exit;
        }
        $cart->remove();
        break;

    case '/cart-submit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $cart->getSessionCarts();
            exit;
        }
        $cart->submit();
        break;

    case '/reservation-submit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $pages->bookings();
            exit;
        }
        $reservation->submit();
        break;

    case '/reservation/cancel':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $pages->bookings();
            exit;
        }
        $reservation->cancel();
        break;

    case '/bookings':
        $pages->bookings();
        break;

    case '/reservation/cancel/guest':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $pages->bookings();
            exit;
        }
        $reservation->cancelGuest(); // you need to create this method
        break;

    case '/admin':
        $pages->admin();
        exit;

    case '/admin/reservations':
        $pages->adminReservations();
        exit;

    case '/admin-login':
        $pages->admin_login();
        exit;

    case '/admin-login-submit':
        $auth->loginAdmin();
        exit;

    default:
        // Router example
        if (preg_match('#^/reservation/cancel/guest/([A-Za-z0-9_-]+)$#', $uri, $matches)) {
            $bookingToken = $matches[1];
            $reservation->showGuestCancelForm($bookingToken);
            exit;
        }
        if (preg_match('#^/reservation/([A-Za-z0-9_-]+)$#', $uri, $matches)) {
            $bookingToken = $matches[1];

            if (!isset($_SESSION['logged_in_user_id'])) {
                header('Location: /registration');
                exit;
            }

            $reservation->show($bookingToken);
            exit;
        }
        $pages->notFound();
        break;
}