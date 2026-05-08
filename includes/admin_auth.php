<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

if (!function_exists('require_admin_login')) {
    function require_admin_login(): void
    {
        if (empty($_SESSION['admin_id'])) {
            header("Location: " . BASE_PATH . "/admin/login.php");
            exit();
        }
    }
}

if (!function_exists('redirect_if_admin_logged_in')) {
    function redirect_if_admin_logged_in(): void
    {
        if (!empty($_SESSION['admin_id'])) {
            header("Location: " . BASE_PATH . "/admin/dashboard.php");
            exit();
        }
    }
}

