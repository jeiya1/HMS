<?php
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
    case '/home':
        require_once '../app/views/home.view.html';
        break;

    case '/login':
        $auth->loginForm();
        break;

    case '/signup':
        $auth->signupForm();
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

    case '/reset-password':
        $auth->resetPasswordForm();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}