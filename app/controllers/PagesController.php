<?php

class PagesController {
    public function privacy() {
        require_once '../app/views/static/privacy.view.html';
    }

    public function terms() {
        require_once '../app/views/static/terms.view.html';
    }
}

?>