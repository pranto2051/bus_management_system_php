<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
include __DIR__ . "/admin_auth.php";
require_admin_login();

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - IUBAT Bus Monitoring</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/admin.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <span class="sidebar-logo"><i class="fa-solid fa-bus"></i></span>
                    <span class="sidebar-title">Admin Panel</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-item <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/dashboard.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'routes') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/routes/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-route"></i>
                            <span>Routes</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'stops') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/stops/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <span>Stops</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'schedules') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/schedules/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-clock"></i>
                            <span>Schedules</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'buses') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/buses/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-bus"></i>
                            <span>Buses</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'drivers') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/drivers/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-id-card"></i>
                            <span>Drivers</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'users') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/users/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item <?php echo ($current_dir === 'locations') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_PATH; ?>/admin/locations/index.php" class="sidebar-menu-link">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>Bus Locations</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?php echo BASE_PATH; ?>/admin/logout.php" class="sidebar-logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="admin-main-wrapper">
            <!-- Top Navigation Bar -->
            <header class="admin-topbar">
                <div class="admin-topbar-content">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="admin-topbar-right">
                        <div class="admin-user-info">
                            <i class="fa-solid fa-user-shield"></i>
                            <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="admin-content">

