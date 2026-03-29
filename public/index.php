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

// TODO
$cartModel = new ReservationCart($GLOBALS['conn']);
// TODO
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

    case '/profile':
        $pages->account();
        break;

    case '/registration':
        $pages->registration();
        break;

    case '/forgot-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->resetPasswordForm(); // handles sending reset email
        } else {
            $auth->forgotPasswordForm(); // shows the form
        }
        break;

    case '/login-submit':
        $auth->login();
        break;

    case '/signup-submit':
        $auth->signup();
        break;

    case '/logout':
        $auth->logout();
        break;

    case '/privacy':
        $pages->privacy();
        break;

    case '/terms':
        $pages->terms();
        break;

    case '/reservation':
        $pages->reservation();
        break;

    case '/reservation-submit':
        $reservation->submit();
        break;

    case '/reset-submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->resetPassword(); // update password in DB
        } else {
            $auth->showResetForm($_GET['token'] ?? null); // show form with token
        }
        break;

    case '/search':
        $search->search();
        break;

    case '/standard':
        $pages->standard();
        break;

    case '/deluxe':
        $pages->deluxe();
        break;

    case '/suite':
        $pages->suite();
        break;

    case '/cart':
        $pages->cart();
        break;

    case '/cart-submit':
        $cart->submit();
        break;

    case '/test':
        require_once '../app/views/static/test.view.php';
        break;

    default:
        $pages->notFound();
        break;
}