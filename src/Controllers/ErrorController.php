<?php
namespace App\Controllers;

use App\Views\View;

class ErrorController {
    public function notFound() {
        http_response_code(404);
        View::render('error/404');
    }

    public function forbidden() {
        http_response_code(403);
        View::render('error/403');
    }
}
