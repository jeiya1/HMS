<?php

class PagesController
{
    public function privacy()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/static/privacy.view.php';
    }

    public function terms()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/static/terms.view.php';
    }
    public function home()
    {
        $logged_in = $this->getAuthState();

        require_once '../app/views/home.view.php';
    }
    public function account() {
        $logged_in = $this->getAuthState();
        require_once '../app/views/auth/account.view.php';
    }

    public function search()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/rooms/search.view.php';
    }

    public function standard()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/rooms/standard.view.php';
    }

    public function deluxe()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/rooms/deluxe.view.php';
    }

    public function suite()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/rooms/suite.view.php';
    }

    public function cart()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/cart/cart.view.php';
    }
    public function reservation()
    {
        $logged_in = $this->getAuthState();
        require_once '../app/views/reservations/reservation.view.html';
    }

    public function registration() {
        $logged_in = $this->getAuthState();
        require_once '../app/views/auth/auth.view.php';
    }

    public function forgotPasswordForm() {
        $logged_in = $this->getAuthState();
        require_once '../app/views/auth/forgot_password.view.php';
    }

    public function notFound()
    {
        $logged_in = $this->getAuthState();
        http_response_code(404);
        require_once '../app/views/static/404.view.php';
    }

    public function getAuthState()
    {
        return isset($_SESSION['logged_in_user_id']);
    }
}