<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IUBAT Bus Monitoring System</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <h1 class="site-title">IUBAT Bus Monitoring</h1>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <nav class="main-nav">
                <a href="<?php echo BASE_PATH; ?>/pages/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <a href="<?php echo BASE_PATH; ?>/pages/routes.php"><i class="fa-solid fa-route"></i> Routes</a>
                <a href="<?php echo BASE_PATH; ?>/pages/map.php"><i class="fa-solid fa-map-location-dot"></i> Map</a>
                <a href="<?php echo BASE_PATH; ?>/pages/profile.php"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="<?php echo BASE_PATH; ?>/pages/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="site-main">

