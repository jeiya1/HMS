<?php
session_start();

require_once '../config/connect.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/PagesController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$auth = new AuthController();
$pages = new PagesController();

switch ($uri) {
    case '/':
    case '/home':
        require_once '../app/views/home.view.php';
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

    case '/privacy':
        $pages->privacy();
        break;

    case '/terms':
        $pages->terms();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}