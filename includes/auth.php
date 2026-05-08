<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header("Location: " . BASE_PATH . "/pages/login.php");
        exit();
    }
}

function redirect_if_logged_in(): void
{
    if (!empty($_SESSION['user_id'])) {
        header("Location: " . BASE_PATH . "/pages/dashboard.php");
        exit();
    }
}


