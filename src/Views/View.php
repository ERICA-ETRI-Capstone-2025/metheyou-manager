<?php
namespace App\Views;

class View {
    public static function render($view, $data = []) {
        extract($data);
        $file = __DIR__ . '/pages/' . $view . '.php';
        if (file_exists($file)) {
            ob_start();
            require $file;
            $content = ob_get_clean();
            require __DIR__ . '/pages/layout.php';
        } else {
            die("View not found: " . $view);
        }
    }
}
