<?php
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

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$auth = new AuthController();
$pages = new PagesController();
$reservation = new ReservationController();

switch ($uri) {
    case '/':
        $pages->home();
        break;

    case '/home':
        $pages->home();
        break;

    case '/login':
        $auth->loginForm();
        break;

    case '/signup':
        $auth->signupForm();
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
        $reservation->reservation();
        break;

    case '/reservation-submit':
        $reservation->submit();
        break;

    case '/password-reset':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->resetPassword(); // update password in DB
        } else {
            $auth->showResetForm($_GET['token'] ?? null); // show form with token
        }
        break;

    case '/search':
        $pages->search();
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

    default:
        $pages->notFound();
        break;
}